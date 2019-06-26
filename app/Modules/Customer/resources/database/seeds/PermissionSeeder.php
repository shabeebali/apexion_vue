<?php
namespace App\Modules\Customer\resources\database\seeds;
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
                'name' => 'create_customer',
                'guard_name' => 'api',
            ],
            [
                'name'=>'edit_pcustomer',
                'guard_name' => 'api',
            ],
            [
                'name'=>'view_customer',
                'guard_name' => 'api',
            ],
            [
                'name'=>'list_customers',
                'guard_name' => 'api',
            ],
            [
                'name'=>'delete_customer',
                'guard_name' => 'api',
            ],
        ]);
    }
}
