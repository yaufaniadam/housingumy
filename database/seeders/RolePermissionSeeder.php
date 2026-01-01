<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Building permissions
            'view_building',
            'create_building',
            'update_building',
            'delete_building',

            // Room permissions
            'view_room',
            'create_room',
            'update_room',
            'delete_room',

            // Facility permissions
            'view_facility',
            'create_facility',
            'update_facility',
            'delete_facility',

            // Reservation permissions
            'view_reservation',
            'create_reservation',
            'update_reservation',
            'delete_reservation',
            'approve_reservation',

            // Payment permissions
            'view_payment',
            'create_payment',
            'update_payment',
            'delete_payment',
            'verify_payment',

            // CheckIn permissions
            'view_checkin',
            'create_checkin',
            'update_checkin',
            'perform_checkin',
            'perform_checkout',

            // Financial permissions
            'view_financial',
            'create_financial',
            'update_financial',
            'delete_financial',
            'view_reports',

            // Unit Kerja permissions
            'view_unit_kerja',
            'create_unit_kerja',
            'update_unit_kerja',
            'delete_unit_kerja',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $adminHousing = Role::firstOrCreate(['name' => 'admin_housing']);
        $adminHousing->givePermissionTo([
            'view_building', 'create_building', 'update_building',
            'view_room', 'create_room', 'update_room',
            'view_facility', 'create_facility', 'update_facility',
            'view_reservation', 'create_reservation', 'update_reservation', 'approve_reservation',
            'view_payment', 'verify_payment',
            'view_checkin', 'perform_checkin', 'perform_checkout',
            'view_unit_kerja',
        ]);

        $finance = Role::firstOrCreate(['name' => 'finance']);
        $finance->givePermissionTo([
            'view_building',
            'view_room',
            'view_reservation',
            'view_payment', 'create_payment', 'update_payment', 'verify_payment',
            'view_financial', 'create_financial', 'update_financial',
            'view_reports',
        ]);

        $unitKerja = Role::firstOrCreate(['name' => 'unit_kerja']);
        $unitKerja->givePermissionTo([
            'view_building',
            'view_room',
            'view_reservation', 'create_reservation',
        ]);

        $guest = Role::firstOrCreate(['name' => 'guest']);
        $guest->givePermissionTo([
            'view_building',
            'view_room',
            'create_reservation',
        ]);

        // Fix: Add roles required by User model and Auth Controllers
        $customer = Role::firstOrCreate(['name' => 'customer']);
        $customer->givePermissionTo([
            'view_building',
            'view_room',
            'create_reservation',
            'view_reservation',
        ]);

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->givePermissionTo([
            'view_building', 'create_building', 'update_building',
            'view_room', 'create_room', 'update_room',
            'view_reservation', 'create_reservation', 'update_reservation', 'approve_reservation',
            'view_payment', 'verify_payment',
            'view_checkin', 'perform_checkin', 'perform_checkout',
            'view_unit_kerja',
        ]);

        // Assign admin role to first user
        $userAdmin = User::first();
        if ($userAdmin) {
            $userAdmin->assignRole('admin');
        }
    }
}
