<x-app-layout>
    <x-slot name="headerTitle">Manajemen Produk/Menu</x-slot>
    <x-slot name="headerSubtitle">Produk yang pernah dibeli akan dinonaktifkan, bukan hard delete.</x-slot>

    @php($money = fn ($amount) => 'Rp'.number_format((int) $amount, 0, ',', '.'))

    <div x-data="{ createOpen: false, editOpen: null }" class="space-y-5">
        <div class="app-card p-4">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <form method="GET" action="{{ route('products.index') }}" class="grid flex-1 gap-3 md:grid-cols-[1fr_220px_auto]">
                    <input name="search" value="{{ request('search') }}" class="app-input" placeholder="Search produk...">
                    <select name="category_id" class="app-input">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) request('category_id') === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary" type="submit">Filter</button>
                </form>
                <button type="button" class="btn-primary" @click="createOpen = true">Tambah produk</button>
            </div>
        </div>

        <section class="app-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Kategori</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="hover:bg-coffee-200/20 dark:hover:bg-slate-800">
                                <td class="table-cell">
                                    <div class="flex items-center gap-3">
                                        <div class="grid h-12 w-12 place-items-center overflow-hidden rounded-2xl bg-coffee-200 text-lg font-black text-coffee-950 dark:bg-slate-800 dark:text-coffee-200">
                                            @if ($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                            @else
                                                {{ strtoupper(substr($product->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-slate-100">{{ $product->name }}</div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $product->slug }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="table-cell">{{ $product->category?->name }}</td>
                                <td class="table-cell">{{ $money($product->price) }}</td>
                                <td class="table-cell">
                                    <span class="badge {{ $product->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" class="btn-secondary px-3 py-2" @click="editOpen = {{ $product->id }}">Edit</button>
                                        <form method="POST" action="{{ route('products.toggle', $product) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-secondary px-3 py-2" type="submit">{{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Hapus produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-danger px-3 py-2" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="table-cell text-center">Belum ada produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @foreach ($products as $product)
            <div x-show="editOpen === {{ $product->id }}" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4">
                <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data" class="app-card w-full max-w-2xl p-6" @click.outside="editOpen = null">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">Edit produk</h2>
                        <button type="button" class="btn-secondary px-3 py-2" @click="editOpen = null">Tutup</button>
                    </div>
                    @include('products.partials.form', ['product' => $product, 'categories' => $categories])
                    <button class="btn-primary mt-5 w-full" type="submit">Simpan perubahan</button>
                </form>
            </div>
        @endforeach

        {{ $products->links() }}

        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="app-card w-full max-w-2xl p-6" @click.outside="createOpen = false">
                @csrf
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">Tambah produk</h2>
                    <button type="button" class="btn-secondary px-3 py-2" @click="createOpen = false">Tutup</button>
                </div>
                @include('products.partials.form', ['product' => null, 'categories' => $categories])
                <button class="btn-primary mt-5 w-full" type="submit">Tambah produk</button>
            </form>
        </div>
    </div>
</x-app-layout>
