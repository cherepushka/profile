<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\ProfileInternal;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use RuntimeException;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    protected function getUserInternalIds(): array
    {
        /**
         * @var Profile $profile
         */
        $profile = auth()->user();
        $internalProfiles = ProfileInternal::where('profile_id', $profile->id)
            ->select('internal_id')
            ->get();

        if ($internalProfiles->isEmpty()) {
            throw new RuntimeException('Пользователь не найден');
        }

        $internalIds = [];
        foreach ($internalProfiles->all() as $profileInternal) {
            $internalIds[] = $profileInternal['internal_id'];
        }

        return $internalIds;
    }
}
