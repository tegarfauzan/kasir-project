<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSetting\UpdateStoreSettingRequest;
use App\Repositories\StoreSettingRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StoreSettingController extends Controller
{
    public function __construct(private readonly StoreSettingRepository $settings)
    {
    }

    public function edit(): View
    {
        return view('store-settings.edit', [
            'settings' => $this->settings->current(),
        ]);
    }

    public function update(UpdateStoreSettingRequest $request): RedirectResponse
    {
        $settings = $this->settings->current();
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $data['logo_path'] = $request->file('logo')->store('store', 'public');
        }

        unset($data['logo']);

        $settings->update($data);

        return back()->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}
