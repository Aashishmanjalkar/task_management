<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        for ($i=1; $i <= 10; $i++) {
            \App\Models\User::factory()->create([
                'name' => fake()->name(),
                'email' => 'test+'.$i. '@example.com',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
