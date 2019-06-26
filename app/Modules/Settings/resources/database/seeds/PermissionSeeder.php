<?php
namespace App\Modules\Settings\resources\database\seeds;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            [
                'name' => 'edit_users',
                'guard_name' => 'api',
            ],
            [
                'name'=>'create_users',
                'guard_name' => 'api',
            ],
            [
                'name'=>'view_users',
                'guard_name' => 'api',
            ],
            [
                'name'=>'list_users',
                'guard_name' => 'api',
            ],
            [
                'name'=>'delete_users',
                'guard_name' => 'api',
            ],
            [
                'name' => 'edit_user_roles',
                'guard_name' => 'api',
            ],
            [
                'name'=>'create_user_roles',
                'guard_name' => 'api',
            ],
            [
                'name'=>'view_user_roles',
                'guard_name' => 'api',
            ],
            [
                'name'=>'list_user_roles',
                'guard_name' => 'api',
            ],
            [
                'name'=>'delete_user_roles',
                'guard_name' => 'api',
            ],
        ]);
    }
}
