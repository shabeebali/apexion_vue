<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\User;
use Konekt\Acl\Models\Role;
use Konekt\Acl\Models\Permission;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         //$this->call(App\Modules\Product\resources\database\seeds\PermissionSeeder::class);
         //$user = User::create(['name'=>'admin','email'=>'admin@admin','password'=>Hash::make('admin')]);
         //$role = Role::create(['name' => 'admin','guard_name'=>'api']);
         //$user->assignRole('admin');
    }
}
