<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $recordTitleAttribute = 'order_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('subtotal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discount_amount')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('shipping_charges')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_method')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payment_status')
                    ->required(),
                Forms\Components\TextInput::make('order_status')
                    ->required(),
                Forms\Components\TextInput::make('address_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('coupon_code')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('coupon_discount')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\DatePicker::make('estimated_delivery_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('delivered_at'),
                Forms\Components\DateTimePicker::make('cancelled_at'),
                Forms\Components\Textarea::make('cancellation_reason')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('is_active')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('is_deleted')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'danger' => 'pending',
                        'success' => 'paid',
                    ]),
                Tables\Columns\BadgeColumn::make('order_status')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('order_status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'online' => 'Online',
                        'cod' => 'Cash on Delivery',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_processing')
                        ->label('Mark as Processing')
                        ->icon('heroicon-o-clock')
                        ->action(fn ($records) => $records->each->update(['order_status' => 'processing']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('mark_shipped')
                        ->label('Mark as Shipped')
                        ->icon('heroicon-o-truck')
                        ->action(fn ($records) => $records->each->update(['order_status' => 'shipped']))
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
