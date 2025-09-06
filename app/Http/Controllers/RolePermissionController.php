<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        return view('role-permission.index');
    }

    /**
     * Get roles data for AJAX requests.
     */
    public function getRolesData(): JsonResponse
    {
        try {
            $roles = Role::withCount('rolePermissions')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'role_name' => $role->role_name,
                        'users_count' => $role->users()->count() ?? 0,
                        'created_at' => $role->created_at->format('d M Y, h:i A'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading roles: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'role_name' => 'required|string|max:255|unique:roles,role_name',
                'role_description' => 'nullable|string|max:500',
            ]);

            $role = Role::create([
                'role_name' => $validated['role_name'],
                'description' => $validated['role_description'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $role
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'role_name' => $role->role_name,
                    'description' => $role->description,
                    'created_at' => $role->created_at->format('d M Y, h:i A'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role): JsonResponse
    {
        try {
            $validated = $request->validate([
                'role_name' => 'required|string|max:255|unique:roles,role_name,' . $role->id,
                'role_description' => 'nullable|string|max:500',
            ]);

            $role->update([
                'role_name' => $validated['role_name'],
                'description' => $validated['role_description'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $role
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            // Check if role has users assigned
            if ($role->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role. Users are still assigned to this role.'
                ], 400);
            }

            // Delete role permissions first
            $role->rolePermissions()->delete();

            // Delete the role
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permissions for a specific role.
     */
    public function getPermissions(Role $role): JsonResponse
    {
        try {
            // Get all available modules
            $modules = $this->getAvailableModules();

            // Get existing permissions for the role
            $existingPermissions = $role->rolePermissions()->get()->keyBy('modules');

            // Build permissions data
            $permissionsData = [];
            foreach ($modules as $module => $subModules) {
                foreach ($subModules as $subModule) {
                    $permissionsData[] = [
                        'module' => $module,
                        'sub_module' => $subModule,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $permissionsData,
                'permissions' => $existingPermissions->toArray()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store permissions for a specific role.
     */
    public function storePermissions(Request $request, Role $role): JsonResponse
    {
        try {
            $validated = $request->validate([
                'permissions' => 'required|array',
                'permissions.*.modules' => 'required|string',
                'permissions.*.view' => 'boolean',
                'permissions.*.create' => 'boolean',
                'permissions.*.edit' => 'boolean',
                'permissions.*.delete' => 'boolean',
                'permissions.*.import' => 'boolean',
                'permissions.*.export' => 'boolean',
            ]);

            // Delete existing permissions
            $role->rolePermissions()->delete();

            // Create new permissions
            foreach ($validated['permissions'] as $permissionData) {
                if (
                    $permissionData['view'] || $permissionData['create'] || $permissionData['edit'] ||
                    $permissionData['delete'] || $permissionData['import'] || $permissionData['export']
                ) {

                    RolePermission::create([
                        'role_id' => $role->id,
                        'modules' => $permissionData['modules'],
                        'view' => $permissionData['view'] ?? false,
                        'create' => $permissionData['create'] ?? false,
                        'edit' => $permissionData['edit'] ?? false,
                        'delete' => $permissionData['delete'] ?? false,
                        'import' => $permissionData['import'] ?? false,
                        'export' => $permissionData['export'] ?? false,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Permissions saved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available modules and sub-modules.
     */
    private function getAvailableModules(): array
    {
        return [
            'Dashboard' => ['Dashboard'],
            'Contacts' => ['Contacts', 'Contact Groups', 'Contact Import'],
            'Companies' => ['Companies', 'Company Import'],
            'Leads' => ['Leads', 'Lead Sources', 'Lead Import'],
            'Deals' => ['Deals', 'Deal Stages', 'Deal Import'],
            'Pipelines' => ['Pipelines', 'Pipeline Stages'],
            'Campaign' => ['Campaign', 'Email Campaign', 'SMS Campaign'],
            'Projects' => ['Projects', 'Project Tasks', 'Project Import'],
            'Tasks' => ['Tasks', 'Task Categories', 'Task Import'],
            'Activity' => ['Activity', 'Activity Types'],
            'Reports' => ['Lead Reports', 'Deal Reports', 'Contact Reports', 'Company Reports'],
            'User Management' => ['Users', 'Roles', 'Permissions'],
            'Settings' => ['General Settings', 'Email Settings', 'System Settings'],
            'Content' => ['Pages', 'Blog', 'Testimonials', 'FAQ'],
            'Support' => ['Tickets', 'Contact Messages'],
        ];
    }

    /**
     * Get role statistics.
     */
    public function getStats(): JsonResponse
    {
        try {
            $stats = [
                'total_roles' => Role::count(),
                'total_permissions' => RolePermission::count(),
                'roles_with_users' => Role::has('users')->count(),
                'recent_roles' => Role::orderBy('created_at', 'desc')->limit(5)->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}
