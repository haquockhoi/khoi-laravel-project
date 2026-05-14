<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserApiController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('userGroup')
            ->latest();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('role', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('user_group_id')) {
            $query->where('user_group_id', $request->user_group_id);
        }

        $users = $query->paginate($request->get('per_page', 10));

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách user thành công.',
            'data' => $users,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function show(User $user)
    {
        $user->load('userGroup');

        return response()->json([
            'success' => true,
            'message' => 'Lấy chi tiết user thành công.',
            'data' => $user,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:admin,user'],
            'user_group_id' => ['nullable', 'exists:user_groups,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
            'user_group_id' => $request->user_group_id,
        ]);

        $user->load('userGroup');

        return response()->json([
            'success' => true,
            'message' => 'Tạo user thành công.',
            'data' => $user,
        ], 201, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'string', 'in:admin,user'],
            'user_group_id' => ['nullable', 'exists:user_groups,id'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => strtolower($request->email),
            'role' => $request->role ?? $user->role,
            'user_group_id' => $request->user_group_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $user->load('userGroup');

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật user thành công.',
            'data' => $user,
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xoá chính tài khoản đang đăng nhập.',
            ], 400, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xoá user thành công.',
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}