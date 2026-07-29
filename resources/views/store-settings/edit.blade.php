<x-app-layout>
    <x-slot name="headerTitle">Pengaturan Toko</x-slot>
    <x-slot name="headerSubtitle">Data ini dipakai di struk dan transfer statis BRI.</x-slot>

    <form method="POST" action="{{ route('store-settings.update') }}" enctype="multipart/form-data" class="app-card max-w-4xl p-6">
        @csrf
        @method('PATCH')

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <label class="app-label" for="store_name">Nama toko</label>
                <input id="store_name" name="store_name" value="{{ old('store_name', $settings->store_name) }}" class="app-input mt-1" required>
            </div>
            <div>
                <label class="app-label" for="phone">Nomor telepon</label>
                <input id="phone" name="phone" value="{{ old('phone', $settings->phone) }}" class="app-input mt-1">
            </div>
            <div class="md:col-span-2">
                <label class="app-label" for="address">Alamat toko</label>
                <textarea id="address" name="address" rows="3" class="app-input mt-1">{{ old('address', $settings->address) }}</textarea>
            </div>
            <div>
                <label class="app-label" for="receipt_footer">Footer struk</label>
                <input id="receipt_footer" name="receipt_footer" value="{{ old('receipt_footer', $settings->receipt_footer) }}" class="app-input mt-1">
            </div>
            <div>
                <label class="app-label" for="logo">Logo toko</label>
                <input id="logo" name="logo" type="file" accept="image/*" class="app-input mt-1">
                @if ($settings->logo_url)
                    <img src="{{ $settings->logo_url }}" alt="{{ $settings->store_name }}" class="mt-3 h-16 w-16 rounded-2xl object-cover">
                @endif
            </div>
            <div>
                <label class="app-label" for="bank_name">Bank</label>
                <input id="bank_name" name="bank_name" value="{{ old('bank_name', $settings->bank_name) }}" class="app-input mt-1" required>
            </div>
            <div>
                <label class="app-label" for="bank_account_number">Nomor rekening</label>
                <input id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $settings->bank_account_number) }}" class="app-input mt-1">
            </div>
            <div class="md:col-span-2">
                <label class="app-label" for="bank_account_name">Nama pemilik rekening</label>
                <input id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $settings->bank_account_name) }}" class="app-input mt-1">
            </div>
        </div>

        <button class="btn-primary mt-6" type="submit">Simpan pengaturan</button>
    </form>
</x-app-layout>
