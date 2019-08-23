<?php
namespace App\Modules\Sale\resources\database\seeds;
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
                'name' => 'create_order',
                'guard_name' => 'api',
            ],
            [
                'name'=>'edit_order',
                'guard_name' => 'api',
            ],
            [
                'name'=>'view_order',
                'guard_name' => 'api',
            ],
            [
                'name'=>'list_order',
                'guard_name' => 'api',
            ],
            [
                'name'=>'delete_order',
                'guard_name' => 'api',
            ],
        ]);
    }
}
