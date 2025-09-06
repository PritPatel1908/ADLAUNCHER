<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['role_name' => 'Super Admin', 'description' => 'Full system access with all permissions'],
            ['role_name' => 'Admin', 'description' => 'Administrative access with most permissions'],
            ['role_name' => 'Manager', 'description' => 'Management level access with limited permissions'],
            ['role_name' => 'User', 'description' => 'Basic user access with minimal permissions'],
            ['role_name' => 'Viewer', 'description' => 'Read-only access to most modules'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::create($roleData);
            $this->assignPermissions($role);
        }
    }

    private function assignPermissions(Role $role): void
    {
        $modules = ['Dashboard', 'Contacts', 'Companies', 'Leads', 'Deals', 'Pipelines', 'Campaign', 'Projects', 'Tasks', 'Activity', 'Reports', 'User Management', 'Settings', 'Content', 'Support'];

        foreach ($modules as $module) {
            $permissions = $this->getModulePermissions($role->role_name, $module);
            RolePermission::create([
                'role_id' => $role->id,
                'modules' => $module,
                'view' => $permissions['view'],
                'create' => $permissions['create'],
                'edit' => $permissions['edit'],
                'delete' => $permissions['delete'],
                'import' => $permissions['import'],
                'export' => $permissions['export'],
            ]);
        }
    }

    private function getModulePermissions($roleName, $module): array
    {
        $permissions = ['view' => false, 'create' => false, 'edit' => false, 'delete' => false, 'import' => false, 'export' => false];

        switch ($roleName) {
            case 'Super Admin':
                $permissions = ['view' => true, 'create' => true, 'edit' => true, 'delete' => true, 'import' => true, 'export' => true];
                break;
            case 'Admin':
                if (in_array($module, ['Dashboard', 'Contacts', 'Companies', 'Leads', 'Deals', 'Campaign', 'Projects', 'Tasks'])) {
                    $permissions = ['view' => true, 'create' => true, 'edit' => true, 'delete' => true, 'import' => true, 'export' => true];
                } elseif (in_array($module, ['Pipelines', 'Activity', 'Reports', 'User Management', 'Content', 'Support'])) {
                    $permissions = ['view' => true, 'create' => true, 'edit' => true, 'delete' => false, 'import' => false, 'export' => true];
                }
                break;
            case 'Manager':
                if (in_array($module, ['Dashboard', 'Contacts', 'Companies', 'Leads', 'Deals', 'Campaign', 'Projects', 'Tasks'])) {
                    $permissions = ['view' => true, 'create' => true, 'edit' => true, 'delete' => false, 'import' => true, 'export' => true];
                } elseif (in_array($module, ['Pipelines', 'Activity', 'Reports', 'Content', 'Support'])) {
                    $permissions = ['view' => true, 'create' => false, 'edit' => false, 'delete' => false, 'import' => false, 'export' => true];
                }
                break;
            case 'User':
                if (in_array($module, ['Dashboard', 'Contacts', 'Leads', 'Deals', 'Projects', 'Tasks', 'Activity', 'Support'])) {
                    $permissions = ['view' => true, 'create' => true, 'edit' => true, 'delete' => false, 'import' => false, 'export' => false];
                } elseif (in_array($module, ['Companies', 'Pipelines', 'Campaign', 'Reports', 'Content'])) {
                    $permissions = ['view' => true, 'create' => false, 'edit' => false, 'delete' => false, 'import' => false, 'export' => false];
                }
                break;
            case 'Viewer':
                if (in_array($module, ['Dashboard', 'Contacts', 'Companies', 'Leads', 'Deals', 'Pipelines', 'Campaign', 'Projects', 'Tasks', 'Activity', 'Reports', 'Content', 'Support'])) {
                    $permissions = ['view' => true, 'create' => false, 'edit' => false, 'delete' => false, 'import' => false, 'export' => false];
                }
                break;
        }

        return $permissions;
    }
}
