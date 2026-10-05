<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $kedai = \App\Models\Kedai::create([
            'name' => 'MOREBREWW',
            'is_active' => true
        ]);

        User::factory()->create([
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'kedai_id' => $kedai->id,
        ]);

        User::factory()->create([
            'name' => 'Kasir',
            'email' => 'kasir@example.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'kedai_id' => $kedai->id,
        ]);
        
        $this->call([
            KategoriSeeder::class,
            ProdukSeeder::class,
        ]);
    }
}
