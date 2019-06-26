<?php
namespace App\Modules\Product\resources\database\seeds;
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
                'name' => 'create_product',
                'guard_name' => 'api',
            ],
            [
                'name'=>'edit_product',
                'guard_name' => 'api',
            ],
            [
                'name'=>'view_product',
                'guard_name' => 'api',
            ],
            [
                'name'=>'list_products',
                'guard_name' => 'api',
            ],
            [
                'name'=>'delete_product',
                'guard_name' => 'api',
            ],
            [
                'name'=>'approve_product',
                'guard_name' => 'api',
            ],
            [
                'name'=>'sync_tally',
                'guard_name' => 'api',
            ],
        ]);
    }
}
