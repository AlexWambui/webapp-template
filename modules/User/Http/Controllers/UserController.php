<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Exception;
use Modules\User\Enums\UserRoles;
use Modules\User\Models\User;
use Modules\User\Http\Requests\UserRequest;
use Modules\User\Http\Resources\UserResource;

class UserController extends Controller
{

    private function getRoleCounts(): array
    {
        $counts = DB::table('users')
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->get();

        $result = [];

        foreach ($counts as $item) {
            $role = UserRoles::tryFrom((int) $item->role);

            if ($role) {
                $result[$role->value] = [
                    'count' => $item->count,
                    'label' => $role->label(),
                    'value' => $role->value,
                ];
            }
        }

        // Sort by label but preserve keys
        uasort($result, function ($a, $b) {
            return $a['label'] <=> $b['label'];
        });

        return $result;
    }

    public function index(Request $request)
    {
        $role_counts = $this->getRoleCounts();

        $query = User::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('role')) {
            $query->filterByRole($request->role);
        }

        $query->orderByRolePriority();

        $users = $query->paginate(20);

        return inertia('app/users/Index', [
            'users' => UserResource::collection($users),
            'role_counts' => $role_counts,
            'filters' => $request->only(['search', 'role'])
        ]);
    }

    public function create()
    {
        return inertia('app/users/Create');
    }

    public function store(UserRequest $request)
    {
        try {
            DB::beginTransaction();

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => $request->status,
            ]);

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "User created successfully"
            ]);

            return to_route('users.index');
        } catch (Exception $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => 'error',
                'message' => "Failed to update user: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function edit(User $user)
    {
        return inertia('app/users/Edit', [
            'user' => new UserResource($user)
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status' => $request->status,
            ]);

            if ($request->password) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            DB::commit();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "User updated successfully"
            ]);

            return to_route('users.index');
        } catch (Exception $e) {
            DB::rollBack();

            Inertia::flash('toast', [
                'type' => "error",
                'message' => "Failed to update user: {$e->getMessage()}"
            ]);

            return back()->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            if (Auth::id() === $user->id) {
                return back()->with([
                    'message' => 'You cannot delete your own account.',
                    'type' => 'error'
                ]);
            }

            $user->delete();

            Inertia::flash('toast', [
                'type' => "success",
                'message' => "User deleted successfully"
            ]);

            return to_route('users.index');
        } catch (Exception $e) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => "Failed to delete user: {$e->getMessage()}"
            ]);
            
            return back();
        }
    }
}