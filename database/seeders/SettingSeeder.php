<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultSettings = [
            'business_name'       => 'Awan Buckle',
            'business_address'    => 'H#1846 Bazar Ali Center Doctor Line Shoes Market Shah Alam Lahore.',
            'business_phone'      => '0321-9999668 / 042-37659668',
            'business_email'      => 'info@awanbuckle.com',
            'business_logo'       => 'assets/images/logo/favicon-32x32.png',
            'business_website'    => 'https://awanbuckle.com',
            'business_tax_number' => 'NTN-1846001-9',
            'currency_symbol'     => 'Rs.',
            'currency_code'       => 'PKR',
        ];

        // Ensure running the seeder does not overwrite existing user-configured values,
        // while using updateOrCreate to guarantee no duplicates.
        foreach ($defaultSettings as $key => $defaultValue) {
            $existing = Setting::where('key', $key)->first();
            if (!$existing) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $defaultValue]
                );
            }
        }

        // Permissions for Settings management
        $permissions = [
            ['name' => 'setting.read',   'description' => 'Read Setting'],
            ['name' => 'setting.create', 'description' => 'Create Setting'],
            ['name' => 'setting.update', 'description' => 'Edit Setting'],
            ['name' => 'setting.delete', 'description' => 'Delete Setting'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                [
                    'name'        => $perm['name'],
                    'description' => $perm['description'],
                    'guard_name'  => 'web',
                ]
            );
        }

        // Grant setting permissions to Super Admin role if available
        $superAdmin = Role::where('id', 1)->orWhere('name', 'Super Admin')->first();
        if ($superAdmin) {
            foreach ($permissions as $perm) {
                if (!$superAdmin->hasPermissionTo($perm['name'])) {
                    $superAdmin->givePermissionTo($perm['name']);
                }
            }
        }

        // Clear settings cache to synchronize
        Setting::clearCache();
    }
}
