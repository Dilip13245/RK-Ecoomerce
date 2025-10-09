<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Latest Orders -->
        <x-filament::section>
            <x-slot name="heading">Latest Orders</x-slot>
            <div class="space-y-3">
                @foreach(\App\Models\Order::with('user')->latest()->limit(10)->get() as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <p class="font-medium text-sm">{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm">₹{{ number_format($order->total_amount, 0) }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                @if($order->order_status === 'delivered') bg-green-100 text-green-800
                                @elseif($order->order_status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Top Products -->
        <x-filament::section>
            <x-slot name="heading">Top Rated Products</x-slot>
            <div class="space-y-3">
                @foreach(\App\Models\Product::where('is_active', true)->orderBy('rating_average', 'desc')->limit(10)->get() as $product)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        @if($product->images && count($product->images) > 0)
                            <img src="{{ asset('storage/products/' . $product->images[0]) }}" 
                                 class="w-12 h-12 rounded object-cover" 
                                 alt="{{ $product->name }}">
                        @endif
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ Str::limit($product->name, 30) }}</p>
                            <p class="text-xs text-gray-500">{{ $product->category->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm">{{ number_format($product->rating_average, 1) }} ⭐</p>
                            <p class="text-xs text-gray-500">{{ $product->rating_count }} reviews</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Low Stock Alert -->
        <x-filament::section>
            <x-slot name="heading">Low Stock Alert</x-slot>
            <div class="space-y-3">
                @foreach(\App\Models\ProductColor::with('product')->where('stock', '<=', 10)->where('stock', '>', 0)->orderBy('stock')->limit(10)->get() as $color)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <p class="font-medium text-sm">{{ Str::limit($color->product->name, 30) }}</p>
                            <p class="text-xs text-gray-500">{{ $color->color_name }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($color->stock <= 5) bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">
                            {{ $color->stock }} left
                        </span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <!-- Recent Reviews -->
        <x-filament::section>
            <x-slot name="heading">Recent Reviews</x-slot>
            <div class="space-y-3">
                @foreach(\App\Models\ProductReview::with(['user', 'product'])->latest()->limit(10)->get() as $review)
                    <div class="p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <p class="font-medium text-sm">{{ $review->user->name }}</p>
                            <span class="text-sm">{{ $review->rating }} ⭐</span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ Str::limit($review->product->name, 40) }}</p>
                        @if($review->review)
                            <p class="text-xs text-gray-500">{{ Str::limit($review->review, 80) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
