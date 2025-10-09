<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('subcategory_id')
                    ->relationship('subcategory', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('₹'),
                Forms\Components\TextInput::make('discounted_price')
                    ->numeric()
                    ->prefix('₹'),
                Forms\Components\TextInput::make('min_quantity')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('images')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->visibility('public')
                    ->imagePreviewHeight('200')
                    ->panelLayout('grid')
                    ->reorderable()
                    ->directory('products')
                    ->columnSpanFull(),
                Forms\Components\KeyValue::make('specifications')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true),
                Forms\Components\Toggle::make('is_deleted')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images')
                    ->getStateUsing(fn ($record) => $record->images && is_array($record->images) ? 'products/' . $record->images[0] : null)
                    ->disk('public')
                    ->label('Image')
                    ->square(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Seller')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discounted_price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rating_average')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('subcategory')
                    ->relationship('subcategory', 'name'),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Seller'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\Filter::make('price')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\TextInput::make('price_to')
                            ->numeric()
                            ->prefix('₹'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['price_from'],
                                fn (Builder $query, $price): Builder => $query->where('price', '>=', $price),
                            )
                            ->when(
                                $data['price_to'],
                                fn (Builder $query, $price): Builder => $query->where('price', '<=', $price),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->action(fn ($record) => $record->update(['is_deleted' => true]))
                    ->successNotificationTitle('Product deleted successfully')
                    ->hidden(fn ($record) => $record->is_deleted),
                Tables\Actions\Action::make('restore')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->action(fn ($record) => $record->update(['is_deleted' => false]))
                    ->successNotificationTitle('Product restored successfully')
                    ->visible(fn ($record) => $record->is_deleted),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(fn ($records) => $records->each->update(['is_deleted' => true]))
                        ->successNotificationTitle('Products deleted successfully'),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('restore')
                        ->label('Restore')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_deleted' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            return response()->streamDownload(function () use ($records) {
                                echo "ID,Name,Price,Category,Status\n";
                                foreach ($records as $record) {
                                    echo "{$record->id},{$record->name},{$record->price},{$record->category->name}," . ($record->is_active ? 'Active' : 'Inactive') . "\n";
                                }
                            }, 'products-' . now()->format('Y-m-d') . '.csv');
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
