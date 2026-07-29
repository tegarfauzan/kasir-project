<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserManagement\ResetUserPasswordRequest;
use App\Http\Requests\UserManagement\StoreUserRequest;
use App\Http\Requests\UserManagement\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function index(Request $request): View
    {
        return view('users.index', [
            'users' => $this->users->paginate($request->only(['search', 'role'])),
            'roles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $isActive = $request->boolean('is_active');

        if ($this->wouldRemoveLastActiveAdmin($user, $data['role'], $isActive)) {
            return back()->with('error', 'Minimal harus ada 1 admin aktif.');
        }

        if ($request->user()->is($user) && ! $isActive) {
            return back()->with('error', 'Admin tidak bisa menonaktifkan dirinya sendiri.');
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $isActive,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);
        $user->syncRoles([$data['role']]);

        return back()->with('success', 'User berhasil diperbarui.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'Admin tidak bisa menonaktifkan dirinya sendiri.');
        }

        if ($user->is_active && $user->hasRole('admin') && $this->users->activeAdminCount() <= 1) {
            return back()->with('error', 'Minimal harus ada 1 admin aktif.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', 'Status user berhasil diperbarui.');
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update(['password' => Hash::make($request->validated('password'))]);

        return back()->with('success', 'Password user berhasil direset.');
    }

    private function wouldRemoveLastActiveAdmin(User $user, string $newRole, bool $newActiveStatus): bool
    {
        if (! $user->hasRole('admin') || ($newRole === 'admin' && $newActiveStatus)) {
            return false;
        }

        return $user->is_active && $this->users->activeAdminCount() <= 1;
    }
}
