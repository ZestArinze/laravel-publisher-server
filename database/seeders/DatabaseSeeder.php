<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // insert a user
        $user = User::updateOrCreate([
            'email'     => 'john@doe.com',
        ],[
            'name'      => 'John Doe',
            'email'     => 'john@doe.com',
            'password'  => Hash::make('testing123'),
            'role'      => Role::USER,
        ]);

        // insert some topics to the DB
        $topics = [
            'body-wash'     => 'Body Wash',
            'conditioner'   => 'Conditioner',
        ];
        foreach($topics as $identifier => $topic) {
            Topic::updateOrCreate([
                'topic'         => $topic,
                'identifier'    => $identifier,
            ],[
                'topic'         => $topic,
                'identifier'    => $identifier,
                'user_id'       => $user->id,
            ]);
        }
    }
}
