<x-app-layout>
    <x-slot name="headerTitle">Kasir / POS</x-slot>
    <x-slot name="headerSubtitle">Checkout cepat, harga tetap dihitung ulang dari database.</x-slot>

    @php
        $money = fn ($amount) => 'Rp'.number_format((int) $amount, 0, ',', '.');
        $productPayload = $products->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (int) $product->price,
            'category' => $product->category?->name,
            'image_url' => $product->image_url,
        ])->values();
    @endphp

    <div
        x-data="posPage(@js($productPayload))"
        class="grid gap-6 xl:grid-cols-[1fr_420px]"
    >
        <section class="space-y-5">
            <form method="GET" action="{{ route('pos.index') }}" class="app-card p-4">
                <div class="grid gap-3 md:grid-cols-[1fr_220px_auto]">
                    <input name="search" value="{{ $search }}" class="app-input" placeholder="Search produk/menu...">
                    <select name="category_id" class="app-input">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategory === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary" type="submit">Filter</button>
                </div>
            </form>

            <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3">
                @forelse ($products as $product)
                    <button
                        type="button"
                        class="group app-card overflow-hidden text-left transition duration-300 hover:-translate-y-1 hover:border-coffee-700 active:translate-y-0"
                        @click="addProduct({{ $product->id }})"
                    >
                        <div class="aspect-[4/3] bg-coffee-200 dark:bg-slate-800">
                            @if ($product->image_url)
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="grid h-full place-items-center text-5xl font-black text-coffee-950/30 dark:text-coffee-200/30">
                                    {{ strtoupper(substr($product->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-extrabold text-slate-900 dark:text-slate-100">{{ $product->name }}</h2>
                                    <p class="mt-1 text-xs font-semibold text-olive-700 dark:text-olive-200">{{ $product->category?->name }}</p>
                                </div>
                                <span class="rounded-full bg-coffee-200 px-3 py-1 text-sm font-extrabold text-coffee-950 dark:bg-coffee-300 dark:text-slate-950">{{ $money($product->price) }}</span>
                            </div>
                            <p class="mt-3 line-clamp-2 text-sm text-slate-500 dark:text-slate-400">{{ $product->description ?: 'Menu siap ditambahkan ke keranjang.' }}</p>
                        </div>
                    </button>
                @empty
                    <div class="app-card p-8 text-center text-sm font-semibold text-slate-500 dark:text-slate-400 sm:col-span-2 2xl:col-span-3">
                        Produk tidak ditemukan.
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="app-card h-fit p-5 xl:sticky xl:top-24">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold text-coffee-950 dark:text-coffee-200">Keranjang</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400"><span x-text="cart.length"></span> item dipilih</p>
                </div>
                <button type="button" class="btn-secondary px-3 py-2" @click="clearCart()" x-show="cart.length">Reset</button>
            </div>

            <div class="mt-5 space-y-3">
                <template x-for="item in cart" :key="item.id">
                    <div class="rounded-2xl border border-coffee-100 p-3 dark:border-slate-800">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-bold text-slate-900 dark:text-slate-100" x-text="item.name"></div>
                                <div class="text-sm text-slate-500 dark:text-slate-400" x-text="formatMoney(item.price)"></div>
                            </div>
                            <button type="button" class="rounded-xl border border-red-200 px-2 py-1 text-xs font-bold text-red-700 transition duration-300 hover:border-red-700 dark:border-red-900 dark:text-red-300" @click="removeItem(item.id)">Hapus</button>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="flex items-center rounded-xl border border-coffee-100 dark:border-slate-800">
                                <button type="button" class="px-3 py-2 font-bold transition duration-300 hover:bg-coffee-200/50 dark:hover:bg-slate-800" @click="decrease(item.id)">-</button>
                                <span class="min-w-10 px-3 text-center font-extrabold" x-text="item.quantity"></span>
                                <button type="button" class="px-3 py-2 font-bold transition duration-300 hover:bg-coffee-200/50 dark:hover:bg-slate-800" @click="increase(item.id)">+</button>
                            </div>
                            <div class="font-extrabold text-coffee-950 dark:text-coffee-200" x-text="formatMoney(item.price * item.quantity)"></div>
                        </div>
                    </div>
                </template>

                <div x-show="!cart.length" class="rounded-2xl border border-dashed border-coffee-200 p-6 text-center text-sm font-semibold text-slate-500 dark:border-slate-800 dark:text-slate-400">
                    Keranjang masih kosong.
                </div>
            </div>

            <form method="POST" action="{{ route('pos.checkout') }}" class="mt-6 space-y-4">
                @csrf
                <input type="hidden" name="items" :value="itemsPayload()">

                <div>
                    <label class="app-label" for="discount_amount">Diskon nominal</label>
                    <input id="discount_amount" name="discount_amount" type="number" min="0" step="1" class="app-input mt-1" x-model.number="discountAmount">
                </div>

                <div>
                    <label class="app-label" for="payment_method">Metode pembayaran</label>
                    <select id="payment_method" name="payment_method" class="app-input mt-1" x-model="paymentMethod">
                        <option value="cash">Cash</option>
                        <option value="transfer">Transfer BRI</option>
                    </select>
                </div>

                <div x-show="paymentMethod === 'cash'">
                    <label class="app-label" for="amount_received">Uang diterima</label>
                    <input id="amount_received" name="amount_received" type="number" min="0" step="1" class="app-input mt-1" x-model.number="amountReceived">
                </div>

                <div x-show="paymentMethod === 'transfer'" class="rounded-2xl border border-coffee-200 bg-cream p-4 text-sm dark:border-slate-800 dark:bg-slate-950">
                    <div class="font-bold text-coffee-950 dark:text-coffee-200">Transfer statis {{ $settings->bank_name }}</div>
                    <div class="mt-1 text-slate-600 dark:text-slate-300">{{ $settings->bank_account_number ?: '-' }} a.n. {{ $settings->bank_account_name ?: $settings->store_name }}</div>
                </div>

                <div class="space-y-2 rounded-2xl bg-coffee-950 p-4 text-sm font-semibold text-coffee-200 dark:bg-slate-950">
                    <div class="flex justify-between gap-3"><span>Subtotal</span><span x-text="formatMoney(subtotal())"></span></div>
                    <div class="flex justify-between gap-3"><span>Diskon</span><span x-text="formatMoney(discount())"></span></div>
                    <div class="flex justify-between gap-3 text-lg text-white dark:text-coffee-200"><span>Total</span><span x-text="formatMoney(total())"></span></div>
                    <div class="flex justify-between gap-3" x-show="paymentMethod === 'cash'"><span>Kembalian</span><span x-text="formatMoney(change())"></span></div>
                </div>

                <button type="submit" class="btn-primary w-full" :disabled="!canCheckout()" :class="{ 'opacity-50': !canCheckout() }">
                    Checkout / Bayar
                </button>
            </form>
        </aside>
    </div>

    @push('scripts')
        <script>
            function posPage(products) {
                return {
                    products,
                    cart: [],
                    discountAmount: 0,
                    paymentMethod: 'cash',
                    amountReceived: null,
                    addProduct(id) {
                        const product = this.products.find((item) => item.id === id);
                        const existing = this.cart.find((item) => item.id === id);

                        if (!product) return;
                        if (existing) {
                            existing.quantity++;
                            return;
                        }

                        this.cart.push({ ...product, quantity: 1 });
                    },
                    increase(id) {
                        const item = this.cart.find((item) => item.id === id);
                        if (item) item.quantity++;
                    },
                    decrease(id) {
                        const item = this.cart.find((item) => item.id === id);
                        if (!item) return;
                        item.quantity = Math.max(1, item.quantity - 1);
                    },
                    removeItem(id) {
                        this.cart = this.cart.filter((item) => item.id !== id);
                    },
                    clearCart() {
                        this.cart = [];
                    },
                    subtotal() {
                        return this.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
                    },
                    discount() {
                        return Math.max(0, Number(this.discountAmount || 0));
                    },
                    total() {
                        return Math.max(0, this.subtotal() - this.discount());
                    },
                    change() {
                        return Math.max(0, Number(this.amountReceived || 0) - this.total());
                    },
                    canCheckout() {
                        if (!this.cart.length || this.discount() > this.subtotal()) return false;
                        if (this.paymentMethod === 'cash' && Number(this.amountReceived || 0) < this.total()) return false;
                        return true;
                    },
                    itemsPayload() {
                        return JSON.stringify(this.cart.map((item) => ({ product_id: item.id, quantity: item.quantity })));
                    },
                    formatMoney(amount) {
                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount || 0);
                    },
                }
            }
        </script>
    @endpush
</x-app-layout>
