<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a new user with the specified details
        User::create([
            'name' => 'Bipin',
            'email' => 'bipin.ingrails@gmail.com',
            'password' => Hash::make('password'), // Encrypt the password
        ]);
    }
}
