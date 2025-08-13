<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Smartisan\Settings\Facades\Settings;
use App\Http\Requests\HomepageRequest;

class HomepageService
{
    public function get(): array
    {
        try {
            return Settings::group('homepage')->all();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception("Failed to fetch homepage settings", 422);
        }
    }

    public function update(HomepageRequest $request)
    {
        try {
            $data = $request->validated();
            Settings::group('homepage')->set($data);
            $homepage = $this->get();

            return response()->json([
                'data' => $homepage,
                'message' => 'Homepage updated successfully',
            ], 200);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return response()->json([
                'message' => $exception->getMessage()
            ], 422);
        }
    }
}
