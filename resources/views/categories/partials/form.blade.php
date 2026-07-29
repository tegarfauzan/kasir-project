<div class="mt-5 space-y-4">
    <div>
        <label class="app-label" for="name-{{ $category?->id ?? 'new' }}">Nama kategori</label>
        <input id="name-{{ $category?->id ?? 'new' }}" name="name" value="{{ old('name', $category?->name) }}" class="app-input mt-1" required>
    </div>
    <div>
        <label class="app-label" for="description-{{ $category?->id ?? 'new' }}">Deskripsi</label>
        <textarea id="description-{{ $category?->id ?? 'new' }}" name="description" rows="3" class="app-input mt-1">{{ old('description', $category?->description) }}</textarea>
    </div>
    <label class="flex items-center gap-3 rounded-2xl border border-coffee-100 p-4 dark:border-slate-800">
        <input type="checkbox" name="is_active" value="1" class="rounded border-coffee-300 text-coffee-950 focus:ring-coffee-700" @checked(old('is_active', $category?->is_active ?? true))>
        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Kategori aktif dan bisa tampil di POS</span>
    </label>
</div>
