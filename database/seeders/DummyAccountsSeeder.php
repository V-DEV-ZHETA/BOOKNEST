<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DummyAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create permissions for staff
        $staffPermissions = [
            'view_any_peminjaman',
            'view_peminjaman',
            'approve_peminjaman',
            'reject_peminjaman',
            'view_any_pengembalian',
            'view_pengembalian',
            'confirm_pengembalian',
            'view_inventori_bukus',
            'view_any_laporan',
            'view_laporans',
        ];

        foreach ($staffPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign permissions to staff
        $staffRole->givePermissionTo($staffPermissions);

        // Create staff user
        $staff = User::firstOrCreate(
            ['email' => 'staff@booknest.com'],
            [
                'name' => 'Staff Perpustakaan',
                'password' => Hash::make('password123'),
                'phone' => '081234567890',
                'status' => 'active',
            ]
        );
        $staff->assignRole('staff');
        $staff->givePermissionTo($staffPermissions);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@booknest.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'phone' => '081234567891',
                'status' => 'active',
            ]
        );
        $admin->assignRole('admin');
        $admin->givePermissionTo(Permission::all());

        // Create dummy user for self-service
        $user = User::firstOrCreate(
            ['email' => 'user@booknest.com'],
            [
                'name' => 'Anggota Perpustakaan',
                'password' => Hash::make('password123'),
                'phone' => '081234567892',
                'address' => 'Jl. Contoh No. 123, Kota Sample',
                'city' => 'Sample City',
                'postal_code' => '12345',
                'status' => 'active',
                'member_number' => 'MEM/' . date('Y') . '/001',
                'member_since' => now(),
            ]
        );
        $user->assignRole('user');

        $this->command->info('Dummy accounts created successfully!');
        $this->command->info('Staff: staff@booknest.com / password123');
        $this->command->info('Admin: admin@booknest.com / admin123');
        $this->command->info('User: user@booknest.com / password123');
    }
}

