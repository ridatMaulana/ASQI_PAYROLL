<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userData = [
            "name" => "tes",
            "email" => "tes@gmail.com",
            "password" => bcrypt("12345678"),
            "peran" => "admin",
        ];
        User::create($userData);
    }
}
