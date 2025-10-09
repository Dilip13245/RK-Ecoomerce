<?php

namespace Database\Seeders;

use App\Models\{Category, SubCategory, Product, ProductColor, User, Order, OrderItem, Coupon, UserAddress};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EcommerceDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        $customer = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '9876543210',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'status' => 'active',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        $seller = User::firstOrCreate(
            ['email' => 'seller@example.com'],
            [
            'name' => 'Tech Store',
            'email' => 'seller@example.com',
            'phone' => '9876543211',
            'password' => Hash::make('password'),
            'user_type' => 'seller',
            'status' => 'active',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        // Create Categories
        $electronics = Category::firstOrCreate(
            ['name' => 'Electronics'],
            [
            'name' => 'Electronics',
            'description' => 'Electronic devices and gadgets',
            'image' => 'electronics.jpg',
            'is_active' => true,
        ]);

        $fashion = Category::firstOrCreate(
            ['name' => 'Fashion'],
            [
            'name' => 'Fashion',
            'description' => 'Clothing and accessories',
            'image' => 'fashion.jpg',
            'is_active' => true,
        ]);

        $home = Category::firstOrCreate(
            ['name' => 'Home & Kitchen'],
            [
            'name' => 'Home & Kitchen',
            'description' => 'Home appliances and kitchen items',
            'image' => 'home.jpg',
            'is_active' => true,
        ]);

        // Create SubCategories
        $mobiles = SubCategory::firstOrCreate(
            ['name' => 'Smartphones', 'category_id' => $electronics->id],
            [
            'category_id' => $electronics->id,
            'name' => 'Smartphones',
            'description' => 'Mobile phones and accessories',
            'image' => 'smartphones.jpg',
            'is_active' => true,
        ]);

        $laptops = SubCategory::firstOrCreate(
            ['name' => 'Laptops', 'category_id' => $electronics->id],
            [
            'category_id' => $electronics->id,
            'name' => 'Laptops',
            'description' => 'Laptops and notebooks',
            'image' => 'laptops.jpg',
            'is_active' => true,
        ]);

        $mensWear = SubCategory::firstOrCreate(
            ['name' => 'Men\'s Clothing', 'category_id' => $fashion->id],
            [
            'category_id' => $fashion->id,
            'name' => 'Men\'s Clothing',
            'description' => 'Clothing for men',
            'image' => 'mens-wear.jpg',
            'is_active' => true,
        ]);

        // Create Products
        $products = [
            [
                'user_id' => $seller->id,
                'name' => 'iPhone 15 Pro Max',
                'price' => 159900,
                'discounted_price' => 149900,
                'min_quantity' => 1,
                'description' => 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system',
                'images' => ['iphone-15-pro-1.jpg', 'iphone-15-pro-2.jpg', 'iphone-15-pro-3.jpg'],
                'specifications' => [
                    'Display' => '6.7-inch Super Retina XDR',
                    'Processor' => 'A17 Pro chip',
                    'Camera' => '48MP Main + 12MP Ultra Wide',
                    'Battery' => 'Up to 29 hours video playback',
                ],
                'category_id' => $electronics->id,
                'subcategory_id' => $mobiles->id,
                'rating_average' => 4.8,
                'rating_count' => 245,
                'is_active' => true,
                'colors' => [
                    ['color_name' => 'Natural Titanium', 'color_code' => '#8B8B8B', 'stock' => 50],
                    ['color_name' => 'Blue Titanium', 'color_code' => '#4A5568', 'stock' => 30],
                    ['color_name' => 'Black Titanium', 'color_code' => '#1A1A1A', 'stock' => 45],
                ],
            ],
            [
                'user_id' => $seller->id,
                'name' => 'Samsung Galaxy S24 Ultra',
                'price' => 134999,
                'discounted_price' => 124999,
                'min_quantity' => 1,
                'description' => 'Premium Android flagship with S Pen, 200MP camera, and AI features',
                'images' => ['samsung-s24-1.jpg', 'samsung-s24-2.jpg'],
                'specifications' => [
                    'Display' => '6.8-inch Dynamic AMOLED 2X',
                    'Processor' => 'Snapdragon 8 Gen 3',
                    'Camera' => '200MP Main + 50MP Telephoto',
                    'RAM' => '12GB',
                ],
                'category_id' => $electronics->id,
                'subcategory_id' => $mobiles->id,
                'rating_average' => 4.7,
                'rating_count' => 189,
                'is_active' => true,
                'colors' => [
                    ['color_name' => 'Titanium Gray', 'color_code' => '#6B7280', 'stock' => 40],
                    ['color_name' => 'Titanium Black', 'color_code' => '#000000', 'stock' => 35],
                ],
            ],
            [
                'user_id' => $seller->id,
                'name' => 'MacBook Pro 14" M3',
                'price' => 199900,
                'discounted_price' => 189900,
                'min_quantity' => 1,
                'description' => 'Powerful laptop with M3 chip, stunning Liquid Retina XDR display',
                'images' => ['macbook-pro-1.jpg', 'macbook-pro-2.jpg'],
                'specifications' => [
                    'Processor' => 'Apple M3 chip',
                    'Display' => '14.2-inch Liquid Retina XDR',
                    'RAM' => '16GB unified memory',
                    'Storage' => '512GB SSD',
                ],
                'category_id' => $electronics->id,
                'subcategory_id' => $laptops->id,
                'rating_average' => 4.9,
                'rating_count' => 156,
                'is_active' => true,
                'colors' => [
                    ['color_name' => 'Space Gray', 'color_code' => '#4A4A4A', 'stock' => 25],
                    ['color_name' => 'Silver', 'color_code' => '#C0C0C0', 'stock' => 20],
                ],
            ],
            [
                'user_id' => $seller->id,
                'name' => 'Dell XPS 15',
                'price' => 149999,
                'discounted_price' => 139999,
                'min_quantity' => 1,
                'description' => 'Premium Windows laptop with InfinityEdge display and powerful performance',
                'images' => ['dell-xps-1.jpg'],
                'specifications' => [
                    'Processor' => 'Intel Core i7-13700H',
                    'Display' => '15.6-inch FHD+',
                    'RAM' => '16GB DDR5',
                    'Storage' => '512GB NVMe SSD',
                ],
                'category_id' => $electronics->id,
                'subcategory_id' => $laptops->id,
                'rating_average' => 4.6,
                'rating_count' => 98,
                'is_active' => true,
                'colors' => [
                    ['color_name' => 'Platinum Silver', 'color_code' => '#E5E5E5', 'stock' => 30],
                ],
            ],
            [
                'user_id' => $seller->id,
                'name' => 'Men\'s Cotton T-Shirt',
                'price' => 799,
                'discounted_price' => 599,
                'min_quantity' => 1,
                'description' => 'Premium quality cotton t-shirt, comfortable and stylish',
                'images' => ['tshirt-1.jpg', 'tshirt-2.jpg'],
                'specifications' => [
                    'Material' => '100% Cotton',
                    'Fit' => 'Regular Fit',
                    'Care' => 'Machine Wash',
                ],
                'category_id' => $fashion->id,
                'subcategory_id' => $mensWear->id,
                'rating_average' => 4.3,
                'rating_count' => 567,
                'is_active' => true,
                'colors' => [
                    ['color_name' => 'Black', 'color_code' => '#000000', 'stock' => 100],
                    ['color_name' => 'White', 'color_code' => '#FFFFFF', 'stock' => 120],
                    ['color_name' => 'Navy Blue', 'color_code' => '#000080', 'stock' => 80],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $colors = $productData['colors'];
            unset($productData['colors']);
            
            $product = Product::create($productData);
            
            foreach ($colors as $color) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'color_name' => $color['color_name'],
                    'color_code' => $color['color_code'],
                    'stock' => $color['stock'],
                ]);
            }
        }

        // Create User Address
        UserAddress::firstOrCreate(
            ['user_id' => $customer->id],
            [
            'user_id' => $customer->id,
            'full_name' => 'John Doe',
            'block_number' => 'A-101',
            'building_name' => 'Green Valley Apartments',
            'area_street' => 'MG Road, Bangalore',
            'state' => 'Karnataka',
            'is_active' => true,
        ]);

        // Create Coupons
        Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            [
            'code' => 'WELCOME10',
            'title' => 'Welcome Offer',
            'type' => 'percentage',
            'value' => 10,
            'min_amount' => 1000,
            'max_discount' => 500,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'is_active' => true,
        ]);

        Coupon::firstOrCreate(
            ['code' => 'FLAT500'],
            [
            'code' => 'FLAT500',
            'title' => 'Flat ₹500 Off',
            'type' => 'fixed',
            'value' => 500,
            'min_amount' => 5000,
            'max_discount' => 500,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(2),
            'is_active' => true,
        ]);

        // Create Orders
        $order = Order::create([
            'order_number' => 'ORD' . time(),
            'user_id' => $customer->id,
            'subtotal' => 150498,
            'discount_amount' => 0,
            'shipping_charges' => 150,
            'total_amount' => 150648,
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'order_status' => 'delivered',
            'address_id' => 1,
            'estimated_delivery_date' => now()->addDays(7),
            'delivered_at' => now()->subDays(2),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'seller_id' => $seller->id,
            'product_id' => 1,
            'product_color_id' => 1,
            'product_title' => 'iPhone 15 Pro Max',
            'color_name' => 'Natural Titanium',
            'color_value' => '#8B8B8B',
            'quantity' => 1,
            'unit_price' => 149900,
            'total_price' => 149900,
            'item_status' => 'delivered',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'seller_id' => $seller->id,
            'product_id' => 5,
            'product_color_id' => 13,
            'product_title' => 'Men\'s Cotton T-Shirt',
            'color_name' => 'Black',
            'color_value' => '#000000',
            'quantity' => 1,
            'unit_price' => 599,
            'total_price' => 599,
            'item_status' => 'delivered',
        ]);
    }
}
