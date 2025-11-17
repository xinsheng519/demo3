<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        DB::table('customers')->insert([
            ['name' => 'Alice', 'email' => 'alice@example.com', 'state' => 'Selangor', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'state' => 'Penang', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('categories')->insert([
            ['name' => 'Electronics', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Books', 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('products')->insert([
            ['name' => 'Laptop', 'category_id' => 1, 'price' => 3000, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Smartphone', 'category_id' => 1, 'price' => 1500, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Novel', 'category_id' => 2, 'price' => 50, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('orders')->insert([
            ['customer_id' => 1, 'order_date' => now(), 'total_amount' => 1550, 'created_at' => now(), 'updated_at' => now()],
            ['customer_id' => 2, 'order_date' => now(), 'total_amount' => 50, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('order_items')->insert([
            ['order_id' => 1, 'product_id' => 2, 'quantity' => 1, 'unit_price' => 1500, 'created_at' => now(), 'updated_at' => now()],
            ['order_id' => 1, 'product_id' => 3, 'quantity' => 1, 'unit_price' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['order_id' => 2, 'product_id' => 3, 'quantity' => 1, 'unit_price' => 50, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
