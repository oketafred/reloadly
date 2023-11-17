<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use OTIFSolutions\ACLMenu\Models\UserRole;
use OTIFSolutions\ACLMenu\Models\Permission;

class DefaultUserPermissionsSync extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //ADMIN PERMISSIONS
        $userRole = UserRole::query()->where(['name' => 'ADMIN'])->first();
        $permissions = Permission::query()->whereIn('menu_item_id', $userRole->menu_items()->pluck('id'))->pluck('id');
        $userRole->permissions()->sync($permissions);

        //RESELLER PERMISSIONS
        $userRole = UserRole::query()->where(['name' => 'RESELLER'])->first();
        $permissions = Permission::query()->whereIn('menu_item_id', $userRole->menu_items()->pluck('id'))->pluck('id');
        $userRole->permissions()->sync($permissions);

        //CUSTOMER PERMISSIONS
        $userRole = UserRole::query()->where(['name' => 'CUSTOMER'])->first();
        $permissions = Permission::query()->whereIn('menu_item_id', $userRole->menu_items()->pluck('id'))->pluck('id');
        $userRole->permissions()->sync($permissions);
    }
}
