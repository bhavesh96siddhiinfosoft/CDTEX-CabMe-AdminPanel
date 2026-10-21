<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class RolesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        // The filter card posts ?search=, which was being ignored — the listing
        // returned every role no matter what was typed.
        $search = trim((string) $request->input('search'));

        $roles = Role::when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate($perPage)
            ->appends($request->all());

        $totalLength = count($roles);

        // The listing shows, per role, who holds it and what it grants, plus a
        // summary strip above the table. Both are read from the roles on this
        // page only, so the query cost does not grow with the account count.
        $pageRoles = Role::with(['permissions:id,name', 'users:id,name,email'])
            ->whereIn('id', $roles->pluck('id'))
            ->get();

        $adminsByRole = $pageRoles->mapWithKeys(function ($role) {
            return [$role->id => $role->users->map(function ($user) {
                return ['name' => $user->name, 'email' => $user->email, 'primary' => $user->id === 1];
            })];
        });

        $permissionsByRole = $pageRoles->mapWithKeys(function ($role) {
            return [$role->id => $role->permissions->pluck('name')];
        });

        $totalRoles  = Role::count();
        $totalAdmins = \App\Models\User::role(Role::pluck('name')->all())->count();
        $unusedRoles = Role::doesntHave('users')->count();

        return view('roles.index', compact(
            'roles', 'totalLength', 'perPage',
            'adminsByRole', 'permissionsByRole', 'totalRoles', 'totalAdmins', 'unusedRoles'
        ));
    }

    public function create()
    {

        $allowedActionMap = [
            'index'   => 'list',
            'list'    => 'list',
            'create'  => 'create',
            'store'   => 'create',
            'edit'    => 'edit',
            'update'  => 'edit',
            'destroy' => 'delete',
            'delete'  => 'delete',
            'show'    => 'view',
        ];

        // load raw permissions
        $permissions = Permission::all()->pluck('name');

        $menus = [];
        foreach ($permissions as $permission) {
            if (!Str::contains($permission, '.')) continue;
            $parts = explode('.', $permission);
            $moduleRaw = $parts[0];
            $actionRaw = end($parts);
            $module = Str::plural($moduleRaw);
            if (!isset($allowedActionMap[$actionRaw])) continue;
            $stdAction = $allowedActionMap[$actionRaw];
            $menus[$module][$stdAction] = $permission;
        }   

        ksort($menus);

        $labelMap = ['list'=>'List','create'=>'Create','edit'=>'Edit','delete'=>'Delete','view'=>'View'];
            
        return view('roles.create', compact('menus','labelMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1'
        ], [
            'permissions.required' => trans('lang.please_select_at_least_one_permission'),
            'permissions.min'      => trans('lang.please_select_at_least_one_permission'),
        ]);

        $role = Role::create([
            'name' => $request->input('name')
        ]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        }

        return redirect()
            ->route('roles.index')
            ->with('message', trans('lang.role_created_successfully'));
    }

    public function edit($id)
    {
        $allowedActionMap = [
            'index'   => 'list',
            'list'    => 'list',
            'create'  => 'create',
            'store'   => 'create',
            'edit'    => 'edit',
            'update'  => 'edit',
            'destroy' => 'delete',
            'delete'  => 'delete',
            'show'    => 'view',
        ];

        // load raw permissions
        $permissions = Permission::all()->pluck('name');

        $menus = [];
        foreach ($permissions as $permission) {
            if (!Str::contains($permission, '.')) continue;
            $parts = explode('.', $permission);
            $moduleRaw = $parts[0];
            $actionRaw = end($parts);
            $module = Str::plural($moduleRaw);
            if (!isset($allowedActionMap[$actionRaw])) continue;
            $stdAction = $allowedActionMap[$actionRaw];
            $menus[$module][$stdAction] = $permission;
        }   

        ksort($menus);

        $labelMap = [
            'list'   => 'List',
            'create' => 'Create',
            'edit'   => 'Edit',
            'delete' => 'Delete',
            'view'   => 'View'
        ];

        // get role's current permissions
        $role = Role::findOrFail($id);
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('menus', 'labelMap', 'role', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'required|array|min:1'
        ], [
            'permissions.required' => trans('lang.please_select_at_least_one_permission'),
            'permissions.min'      => trans('lang.please_select_at_least_one_permission'),
        ]);

        $role->update([
            'name' => $request->input('name')
        ]);

        if ($request->filled('permissions')) {
            $role->syncPermissions($request->input('permissions'));
        } else {
            $role->syncPermissions([]);
        }

        return redirect()
            ->route('roles.index')
            ->with('message', trans('lang.role_updated_successfully'));
    }

   
    public function delete($id)
    {
        if (!empty($id)) {
            // Always convert to array (single or multiple)
            $ids = explode(',', $id);

            foreach ($ids as $roleId) {
                if ($roleId != 1) { // prevent super admin delete
                    $role = Role::findOrFail($roleId);
                    $role->delete();
                }
            }
        }

        return redirect()->route('roles.index')->with('success', trans('lang.role_deleted'));
    }

}
