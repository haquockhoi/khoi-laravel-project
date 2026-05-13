<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    public function index()
    {
        return view('user-groups.index');
    }

    public function data(Request $request)
    {
        $columns = [
            0 => 'id',
            1 => 'name',
            2 => 'description',
            3 => 'is_fullaccess',
            4 => 'id',
            5 => 'id',
        ];

        $totalData = UserGroup::count();

        $query = UserGroup::withCount('permissions');

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $totalFiltered = $query->count();

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        $orderDirection = $request->input('order.0.dir', 'desc');

        if (in_array($orderColumn, ['id', 'name', 'description', 'is_fullaccess'])) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->latest();
        }

        $userGroups = $query
            ->offset($request->input('start', 0))
            ->limit($request->input('length', 10))
            ->get();

        $data = [];

        foreach ($userGroups as $group) {
            $fullAccess = $group->is_fullaccess
                ? '<span class="badge bg-success">Yes</span>'
                : '<span class="badge bg-secondary">No</span>';

            $permissions = '<span class="badge bg-primary">' . $group->permissions_count . ' permissions</span>';

            $action = '
                <a href="' . route('user-groups.permissions', $group) . '" class="btn btn-info btn-sm">
                    Permissions
                </a>

                <a href="' . route('user-groups.edit', $group) . '" class="btn btn-warning btn-sm">
                    Edit
                </a>

                <form action="' . route('user-groups.destroy', $group) . '"
                      method="POST"
                      class="d-inline delete-user-group-form"
                      data-id="' . $group->id . '"
                      data-name="' . e($group->name) . '">
                    <input type="hidden" name="_token" value="' . csrf_token() . '">
                    <input type="hidden" name="_method" value="DELETE">

                    <button type="submit" class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>
            ';

            $data[] = [
                'id' => $group->id,
                'name' => '<strong>' . e($group->name) . '</strong>',
                'description' => e($group->description ?? '-'),
                'is_fullaccess' => $fullAccess,
                'permissions_count' => $permissions,
                'action' => $action,
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        return view('user-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:user_groups,name',
            'description' => 'nullable|string',
            'is_fullaccess' => 'nullable|boolean',
        ]);

        $userGroup = UserGroup::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_fullaccess' => $request->has('is_fullaccess'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tạo nhóm user thành công!',
                'redirect' => route('user-groups.index'),
                'data' => $userGroup,
            ]);
        }

        return redirect()
            ->route('user-groups.index')
            ->with('success', 'Tạo nhóm user thành công!');
    }

    public function edit(UserGroup $userGroup)
    {
        return view('user-groups.edit', compact('userGroup'));
    }

    public function update(Request $request, UserGroup $userGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:user_groups,name,' . $userGroup->id,
            'description' => 'nullable|string',
            'is_fullaccess' => 'nullable|boolean',
        ]);

        $userGroup->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_fullaccess' => $request->has('is_fullaccess'),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nhóm user thành công!',
                'redirect' => route('user-groups.index'),
                'data' => $userGroup,
            ]);
        }

        return redirect()
            ->route('user-groups.index')
            ->with('success', 'Cập nhật nhóm user thành công!');
    }

    public function destroy(UserGroup $userGroup, Request $request)
    {
        $userGroup->permissions()->delete();
        $userGroup->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Xoá nhóm user thành công!',
            ]);
        }

        return redirect()
            ->route('user-groups.index')
            ->with('success', 'Xoá nhóm user thành công!');
    }

    public function permissions(UserGroup $userGroup)
    {
        return view('user-groups.permissions', compact('userGroup'));
    }

    public function permissionsAjax(UserGroup $userGroup)
    {
        $permissions = $this->getPermissionList();

        $selectedPermissions = $userGroup->permissions()
            ->get()
            ->map(function ($permission) {
                return $permission->controller . '@' . $permission->action;
            })
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'group' => [
                    'id' => $userGroup->id,
                    'name' => $userGroup->name,
                    'is_fullaccess' => (bool) $userGroup->is_fullaccess,
                ],
                'permissions' => $permissions,
                'selectedPermissions' => $selectedPermissions,
            ],
        ]);
    }

    public function updatePermissions(Request $request, UserGroup $userGroup)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $userGroup->permissions()->delete();

        foreach ($request->permissions ?? [] as $permission) {
            [$controller, $action] = explode('@', $permission);

            UserGroupPermission::create([
                'user_group_id' => $userGroup->id,
                'controller' => $controller,
                'action' => $action,
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phân quyền thành công!',
            ]);
        }

        return redirect()
            ->route('user-groups.permissions', $userGroup)
            ->with('success', 'Cập nhật phân quyền thành công!');
    }

    private function getPermissionList()
    {
        return [
            'DashboardController' => [
                'index' => 'Xem dashboard',
            ],

            'UserController' => [
                'index' => 'Xem danh sách user',
                'create' => 'Mở form thêm user',
                'store' => 'Lưu user mới',
                'edit' => 'Mở form sửa user',
                'update' => 'Cập nhật user',
                'destroy' => 'Xoá user',
            ],

            'UserGroupController' => [
                'index' => 'Xem danh sách nhóm user',
                'create' => 'Mở form thêm nhóm user',
                'store' => 'Lưu nhóm user mới',
                'edit' => 'Mở form sửa nhóm user',
                'update' => 'Cập nhật nhóm user',
                'destroy' => 'Xoá nhóm user',
                'permissions' => 'Xem phân quyền nhóm',
                'updatePermissions' => 'Cập nhật phân quyền nhóm',
            ],

            'CategoryController' => [
                'index' => 'Xem danh sách danh mục tin tức',
                'create' => 'Mở form thêm danh mục tin tức',
                'store' => 'Lưu danh mục tin tức mới',
                'edit' => 'Mở form sửa danh mục tin tức',
                'update' => 'Cập nhật danh mục tin tức',
                'destroy' => 'Xoá danh mục tin tức',
            ],

            'NewsController' => [
                'index' => 'Xem danh sách tin tức',
                'create' => 'Mở form thêm tin tức',
                'store' => 'Lưu tin tức mới',
                'edit' => 'Mở form sửa tin tức',
                'update' => 'Cập nhật tin tức',
                'destroy' => 'Xoá tin tức',
            ],

            'ProfileController' => [
                'edit' => 'Xem profile',
                'update' => 'Cập nhật profile',
                'destroy' => 'Xoá tài khoản',
            ],

            'PageController' => [
                'form_advanced' => 'Truy cập Form Advanced',
                'ecommerce_customers' => 'Truy cập Ecommerce Customers',
                'ecommerce_checkout' => 'Truy cập Ecommerce Checkout',
                'email_template_basic' => 'Truy cập Email Template Basic',
                'email_template_billing' => 'Truy cập Email Template Billing',
            ],
        ];
    }
}