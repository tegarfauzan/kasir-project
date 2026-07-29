<div class="mt-5 grid gap-4 md:grid-cols-2">
    <div>
        <label class="app-label" for="name-{{ $managedUser?->id ?? 'new' }}">Nama</label>
        <input id="name-{{ $managedUser?->id ?? 'new' }}" name="name" value="{{ old('name', $managedUser?->name) }}" class="app-input mt-1" required>
    </div>
    <div>
        <label class="app-label" for="email-{{ $managedUser?->id ?? 'new' }}">Email</label>
        <input id="email-{{ $managedUser?->id ?? 'new' }}" name="email" type="email" value="{{ old('email', $managedUser?->email) }}" class="app-input mt-1" required>
    </div>
    <div>
        <label class="app-label" for="role-{{ $managedUser?->id ?? 'new' }}">Role</label>
        <select id="role-{{ $managedUser?->id ?? 'new' }}" name="role" class="app-input mt-1" required>
            @foreach ($roles as $role)
                <option value="{{ $role->name }}" @selected(old('role', $managedUser?->roles->first()?->name ?? 'cashier') === $role->name)>{{ ucfirst($role->name) }}</option>
            @endforeach
        </select>
    </div>
    <label class="flex items-center gap-3 rounded-2xl border border-coffee-100 p-4 dark:border-slate-800">
        <input type="checkbox" name="is_active" value="1" class="rounded border-coffee-300 text-coffee-950 focus:ring-coffee-700" @checked(old('is_active', $managedUser?->is_active ?? true))>
        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">User aktif</span>
    </label>

    <div>
        <label class="app-label" for="password-{{ $managedUser?->id ?? 'new' }}">Password {{ $isEdit ? '(opsional)' : '' }}</label>
        <input id="password-{{ $managedUser?->id ?? 'new' }}" name="password" type="password" class="app-input mt-1" @required(! $isEdit)>
    </div>
    <div>
        <label class="app-label" for="password-confirm-{{ $managedUser?->id ?? 'new' }}">Konfirmasi password</label>
        <input id="password-confirm-{{ $managedUser?->id ?? 'new' }}" name="password_confirmation" type="password" class="app-input mt-1" @required(! $isEdit)>
    </div>
</div>
