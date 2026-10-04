<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'ÉLITE Admin',
            'email' => 'admin@elite.com',
            'phone' => '+383 44 100 200',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Elena Marks',
            'email' => 'elena.marks@example.com',
            'phone' => '+41 79 555 0142',
            'role' => 'customer',
        ]);

        User::factory()->create([
            'name' => 'James Whitfield',
            'email' => 'james.whitfield@example.com',
            'phone' => '+44 7700 900321',
            'role' => 'customer',
        ]);

        // Additional customer accounts to give reviews a realistic spread of authors.
        User::factory(10)->create([
            'role' => 'customer',
        ]);
    }
}
