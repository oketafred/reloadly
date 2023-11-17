<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use OTIFSolutions\ACLMenu\Models\UserRole;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (User::query()->find(1) === null) {
            User::query()
                ->create([
                    'id' => 1,
                    'name' => 'Administrator',
                    'email' => 'admin@system.com',
                    'password' => Hash::make('admin'),
                    'username' => 'administrator',
                    'user_role_id' => UserRole::query()->where('name', 'ADMIN')->first()['id'],
                ]);
        }
    }
}
