<x-app-layout>
    <x-slot name="headerTitle">Manajemen Kategori</x-slot>
    <x-slot name="headerSubtitle">Kategori yang masih punya produk tidak bisa dihapus.</x-slot>

    <div x-data="{ createOpen: false, editOpen: null }" class="space-y-5">
        <div class="app-card p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <form method="GET" action="{{ route('categories.index') }}" class="grid flex-1 gap-3 md:grid-cols-[1fr_auto]">
                    <input name="search" value="{{ request('search') }}" class="app-input" placeholder="Search kategori...">
                    <button class="btn-primary" type="submit">Filter</button>
                </form>
                <button type="button" class="btn-primary" @click="createOpen = true">Tambah kategori</button>
            </div>
        </div>

        <section class="app-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="table-head">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Slug</th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="hover:bg-coffee-200/20 dark:hover:bg-slate-800">
                                <td class="table-cell font-bold">{{ $category->name }}</td>
                                <td class="table-cell">{{ $category->slug }}</td>
                                <td class="table-cell">{{ $category->products_count }}</td>
                                <td class="table-cell">
                                    <span class="badge {{ $category->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300' }}">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="table-cell">
                                    <div class="flex flex-wrap gap-2">
                                        <button type="button" class="btn-secondary px-3 py-2" @click="editOpen = {{ $category->id }}">Edit</button>
                                        <form method="POST" action="{{ route('categories.toggle', $category) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn-secondary px-3 py-2" type="submit">{{ $category->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn-danger px-3 py-2" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="table-cell text-center">Belum ada kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @foreach ($categories as $category)
            <div x-show="editOpen === {{ $category->id }}" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4">
                <form method="POST" action="{{ route('categories.update', $category) }}" class="app-card w-full max-w-xl p-6" @click.outside="editOpen = null">
                    @csrf
                    @method('PATCH')
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">Edit kategori</h2>
                        <button type="button" class="btn-secondary px-3 py-2" @click="editOpen = null">Tutup</button>
                    </div>
                    @include('categories.partials.form', ['category' => $category])
                    <button class="btn-primary mt-5 w-full" type="submit">Simpan perubahan</button>
                </form>
            </div>
        @endforeach

        {{ $categories->links() }}

        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center bg-slate-950/60 p-4">
            <form method="POST" action="{{ route('categories.store') }}" class="app-card w-full max-w-xl p-6" @click.outside="createOpen = false">
                @csrf
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-xl font-extrabold text-coffee-950 dark:text-coffee-200">Tambah kategori</h2>
                    <button type="button" class="btn-secondary px-3 py-2" @click="createOpen = false">Tutup</button>
                </div>
                @include('categories.partials.form', ['category' => null])
                <button class="btn-primary mt-5 w-full" type="submit">Tambah kategori</button>
            </form>
        </div>
    </div>
</x-app-layout>
