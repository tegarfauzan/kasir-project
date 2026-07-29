<?php

namespace App\Repositories;

use App\Models\StoreSetting;

class StoreSettingRepository
{
    public function current(): StoreSetting
    {
        return StoreSetting::current();
    }
}
