<x-app-layout>
    <x-slot name="headerTitle">Manajemen Pengguna</x-slot>
    <x-slot name="headerSubtitle">Role sederhana: admin dan cashier.</x-slot>

    <div x-data="{ createOpen: false, editOpen: null, passwordOpen: null }" class="space-y-5">
        <div class="app-card p-4">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <form method="GET" action="{{ route('users.index') }}" class="grid flex-1 gap-3 md:grid-cols-[1fr_220px_auto]">
                    <input name="search" value="{{ request('search') }}" class="app-input" placeholder="Search user...">
                    <select name="role" class="app-input">
                        <option value="">Semua role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" @selected(request('role') === $role->name)>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary" type="submit">Filter</button>
                </form>
                <button type="button" class="btn-primary" @click="createOpen = true">Tambah user</button>
            </div>
        </div>

        <section class="app-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $managedUser)
                            <tr class="hover:bg-coffee-200/20 dark:hover:bg-slate-800">
                                <td class="table-cell">
                                    <div class="font-bold text-slate-900 dark:text-slate-100">{{ $managedUser->name }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $managedUser->email }}</div>
                                </td>
                                <td class="table-cell">{{ ucfirst($managedUser->roles->first()?->name ?? '-') }}</td>
                                <td class="table-cell">
                                    <span class="badge {{ $managedUser->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $managedUser->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" class="btn-secondary px-3 py-2" @click="editOpen = {{ $managedUser->id }}">Edit</button>
                                        <button type="button" class="btn-secondary px-3 py-2" @click="passwordOpen = {{ $managedUser->id }}">Reset password</button>
                                        <form method="POST" action="{{ route('users.toggle', $managedUser) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-secondary px-3 py-2" type="submit">{{ $managedUser->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="table-cell text-center">Belum ada user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @foreach ($users as $managedUser)
            <div x-show="editOpen === {{ $managedUser->id }}" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4">
                <form method="POST" action="{{ route('users.update', $managedUser) }}" class="app-card w-full max-w-2xl p-6" @click.outside="editOpen = null">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">Edit user</h2>
                        <button type="button" class="btn-secondary px-3 py-2" @click="editOpen = null">Tutup</button>
                    </div>
                    @include('users.partials.form', ['managedUser' => $managedUser, 'roles' => $roles, 'isEdit' => true])
                    <button class="btn-primary mt-5 w-full" type="submit">Simpan perubahan</button>
                </form>
            </div>

            <div x-show="passwordOpen === {{ $managedUser->id }}" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4">
                <form method="POST" action="{{ route('users.password', $managedUser) }}" class="app-card w-full max-w-md p-6" @click.outside="passwordOpen = null">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">Reset password</h2>
                        <button type="button" class="btn-secondary px-3 py-2" @click="passwordOpen = null">Tutup</button>
                    </div>
                    <div class="mt-5 space-y-4">
                        <div>
                            <label class="app-label" for="password-reset-{{ $managedUser->id }}">Password baru</label>
                            <input id="password-reset-{{ $managedUser->id }}" name="password" type="password" class="app-input mt-1" required>
                        </div>
                        <div>
                            <label class="app-label" for="password-confirm-reset-{{ $managedUser->id }}">Konfirmasi password</label>
                            <input id="password-confirm-reset-{{ $managedUser->id }}" name="password_confirmation" type="password" class="app-input mt-1" required>
                        </div>
                    </div>
                    <button class="btn-primary mt-5 w-full" type="submit">Reset password</button>
                </form>
            </div>
        @endforeach

        {{ $users->links() }}

        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4">
            <form method="POST" action="{{ route('users.store') }}" class="app-card w-full max-w-2xl p-6" @click.outside="createOpen = false">
                @csrf
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">Tambah user</h2>
                    <button type="button" class="btn-secondary px-3 py-2" @click="createOpen = false">Tutup</button>
                </div>
                @include('users.partials.form', ['managedUser' => null, 'roles' => $roles, 'isEdit' => false])
                <button class="btn-primary mt-5 w-full" type="submit">Tambah user</button>
            </form>
        </div>
    </div>
</x-app-layout>
