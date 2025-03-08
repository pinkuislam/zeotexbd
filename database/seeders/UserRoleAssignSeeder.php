<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserRoleAssignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::get();
        foreach ($users as $key => $user) {
            if ($key == 0){
                $user->syncRoles(['Super Admin']);
            }else{
                $user->syncRoles([$user->role]);

            }
        }
    }
}
