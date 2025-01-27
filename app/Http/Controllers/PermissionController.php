<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Events\PermissionUpdated;
use App\Events\RoleUpdated;

class PermissionController extends Controller
{
    public function fetchUser()
    {

        $users = DB::table('users')->select('id', 'username')
            ->get();
        $permissions = DB::table('permissions')->select('name as permission')
            ->get();

        return response()->json(['users' => $users, 'permissions' => $permissions]);
    }

    public function fetchRole()
    {
        $roles = DB::table('roles')->select('id','name')
            ->get();
        $permissions = DB::table('permissions')->select('name as permission')
            ->get();

        return response()->json(['roles' => $roles, 'permissions' => $permissions]);
    }

    public function updateRole(Request $request)
    {
        $roleName = $request->input('role');
        $permissions = $request->input('permissions');
        $role = Role::findByName($roleName);

        $role->syncPermissions($permissions);

        $users_with_role = DB::table('users')->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->select('users.*', 'model_has_roles.role_id as role_id')
            ->get();
        foreach ($users_with_role as $user_with_role) {
            if ($user_with_role->role_id == $role->id) {
                $user = User::find($user_with_role->id);
                $user->syncPermissions($permissions);
            }
        }

        broadcast(new PermissionUpdated($role, $permissions));

        return redirect('/permission')->with('success', 'Permission Updated');
    }

    public function assignRole(Request $request)
    {

        $user_id = $request->input('user_id');
        $roleName = $request->input('role');

        $user = User::findById($user_id);
        $role = Role::findByName($roleName);

        $user->syncRoles($role);

        broadcast(new RoleUpdated($user, $role));

        return response();
    }

    public function updateUser(Request $request)
    {
        $user_id = $request->input('user');
        $permissions = $request->input('permissions');

        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->syncPermissions($permissions);

        broadcast(new PermissionUpdated('Trigger event'));

        return response()->json(['message' => 'Permissions updated successfully']);
    }


    public function createPermission(Request $request)
    {

        $permission = $request->input('permission');
        Permission::create(['name' => $permission]);

        return redirect('/permission')->with('success', 'Permission Created');
    }

    public function fetchUserPermission($user_id)
    {
        $permissions = DB::table('model_has_permissions')->join('permissions', 'permission_id', '=', 'id')
            ->where('model_id', '=', $user_id)
            ->pluck('permissions.name');

        return response()->json(['permissions' => $permissions]);
    }

    public function fetchRolePermission($role_id)
    {

        $permissions = DB::table('role_has_permissions')->join('permissions', 'permission_id', '=', 'id')
        ->where('role_id', '=', $role_id)
        ->pluck('permissions.name');

        return response()->json(['permissions' => $permissions]);
    }

    public function showPermission(){

        return view('forms');

    }
}
