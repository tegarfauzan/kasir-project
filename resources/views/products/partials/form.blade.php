<div class="mt-5 grid gap-4 md:grid-cols-2">
    <div>
        <label class="app-label" for="name-{{ $product?->id ?? 'new' }}">Nama produk</label>
        <input id="name-{{ $product?->id ?? 'new' }}" name="name" value="{{ old('name', $product?->name) }}" class="app-input mt-1" required>
    </div>
    <div>
        <label class="app-label" for="category-{{ $product?->id ?? 'new' }}">Kategori</label>
        <select id="category-{{ $product?->id ?? 'new' }}" name="category_id" class="app-input mt-1" required>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('category_id', $product?->category_id) === $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="app-label" for="price-{{ $product?->id ?? 'new' }}">Harga</label>
        <input id="price-{{ $product?->id ?? 'new' }}" name="price" type="number" min="0" step="1" value="{{ old('price', $product?->price ? (int) $product->price : 0) }}" class="app-input mt-1" required>
    </div>
    <div>
        <label class="app-label" for="image-{{ $product?->id ?? 'new' }}">Gambar produk</label>
        <input id="image-{{ $product?->id ?? 'new' }}" name="image" type="file" accept="image/*" class="app-input mt-1">
    </div>
    <div class="md:col-span-2">
        <label class="app-label" for="description-{{ $product?->id ?? 'new' }}">Deskripsi</label>
        <textarea id="description-{{ $product?->id ?? 'new' }}" name="description" rows="3" class="app-input mt-1">{{ old('description', $product?->description) }}</textarea>
    </div>
    <label class="flex items-center gap-3 rounded-2xl border border-coffee-100 p-4 dark:border-slate-800 md:col-span-2">
        <input type="checkbox" name="is_active" value="1" class="rounded border-coffee-300 text-coffee-950 focus:ring-coffee-700" @checked(old('is_active', $product?->is_active ?? true))>
        <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Produk aktif dan tampil di POS</span>
    </label>
</div>
