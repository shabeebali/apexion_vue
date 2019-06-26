<?php
namespace App\Modules\Taxonomy\resources\database\seeds;
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
                'name' => 'create_category',
                'guard_name' => 'api',
            ],
            [
                'name'=>'edit_category',
                'guard_name' => 'api',
            ],
            [
                'name'=>'view_category',
                'guard_name' => 'api',
            ],
            [
                'name'=>'list_category',
                'guard_name' => 'api',
            ],
            [
                'name'=>'delete_category',
                'guard_name' => 'api',
            ],
        ]);
    }
}
