<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'balance' => 1000000,
        ]);
        
        // Create reseller user
        User::create([
            'name' => 'Reseller',
            'email' => 'reseller@example.com',
            'password' => Hash::make('password'),
            'role' => 'reseller',
            'balance' => 500000,
        ]);
        
        // Create customer user
        User::create([
            'name' => 'Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'balance' => 250000,
        ]);
    }
}
