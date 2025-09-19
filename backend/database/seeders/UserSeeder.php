<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\User\Infra\Persistence\Models\User as ModelsUser;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        ModelsUser::query()->delete();

        ModelsUser::insert([
            [
                'id' => 1,
                'name' => 'Alice',
                'email' => 'alice@example.com',
                'password' => Hash::make('123456'),
            ],
            [
                'id' => 2,
                'name' => 'Bob',
                'email' => 'bob@example.com',
                'password' => Hash::make('123456'),
            ],
            [
                'id' => 3,
                'name' => 'Charlie',
                'email' => 'charlie@example.com',
                'password' => Hash::make('123456'),
            ],
            [
                'id' => 4,
                'name' => 'Dave',
                'email' => 'dave@example.com',
                'password' => Hash::make('123456'),
            ],
        ]);
    }
}
