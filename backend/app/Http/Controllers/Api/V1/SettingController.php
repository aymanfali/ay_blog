<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SettingTranslation;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Setting::withTranslation();

        // Apply filters
        if ($request->has('group')) {
            $query->where('group', $request->group);
        }

        if ($request->has('key')) {
            $query->where('key', $request->key);
        }

        $settings = $query->get();

        return $this->ok($settings, 'messages.success');
    }

    public function show(Setting $setting)
    {
        $setting->load(['translations' => function ($q) {
            $q->where('locale', app()->getLocale())
                ->orWhere('locale', config('app.fallback_locale'));
        }]);

        return $this->ok($setting, 'messages.success');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group' => 'required|string|max:255',
            'key' => 'required|string|max:255',
            'value.en' => 'nullable|string',
            'value.ar' => 'nullable|string',
        ]);

        // Check if setting already exists
        $existingSetting = Setting::where('group', $validated['group'])
            ->where('key', $validated['key'])
            ->first();

        if ($existingSetting) {
            return $this->validationErrorResponse([
                'setting' => ['A setting with this group and key already exists.']
            ]);
        }

        DB::beginTransaction();

        $setting = Setting::create([
            'group' => $validated['group'],
            'key' => $validated['key'],
        ]);

        // Create translations using HasTranslations trait
        foreach (['en', 'ar'] as $locale) {
            if (isset($validated['value'][$locale])) {
                SettingTranslation::create([
                    'setting_id' => $setting->id,
                    'locale' => $locale,
                    'value' => $validated['value'][$locale],
                ]);
            }
        }

        DB::commit();

        $setting->load('translations');

        return $this->created($setting, 'messages.setting_created');
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'group' => 'sometimes|string|max:255',
            'key' => 'sometimes|string|max:255',
            'value.en' => 'nullable|string',
            'value.ar' => 'nullable|string',
        ]);

        // Check if group/key combination already exists (excluding current setting)
        if (isset($validated['group']) || isset($validated['key'])) {
            $existingSetting = Setting::where('group', $validated['group'] ?? $setting->group)
                ->where('key', $validated['key'] ?? $setting->key)
                ->where('id', '!=', $setting->id)
                ->first();

            if ($existingSetting) {
                return $this->validationErrorResponse([
                    'setting' => ['A setting with this group and key already exists.']
                ]);
            }
        }

        DB::beginTransaction();

        $setting->update([
            'group' => $validated['group'] ?? $setting->group,
            'key' => $validated['key'] ?? $setting->key,
        ]);

        // Update translations using HasTranslations trait
        if (isset($validated['value'])) {
            foreach (['en', 'ar'] as $locale) {
                $translation = $setting->translations()->where('locale', $locale)->first();

                if (isset($validated['value'][$locale])) {
                    if ($translation) {
                        $translation->update([
                            'value' => $validated['value'][$locale],
                        ]);
                    } else {
                        SettingTranslation::create([
                            'setting_id' => $setting->id,
                            'locale' => $locale,
                            'value' => $validated['value'][$locale],
                        ]);
                    }
                }
            }
        }

        DB::commit();

        $setting->load('translations');

        return $this->ok($setting, 'messages.setting_updated');
    }

    public function destroy(Setting $setting)
    {
        $deletedId = $setting->id;
        $setting->delete();

        return $this->deletedResponse(
            null,
            ['deleted_id' => $deletedId, 'deleted_at' => now()->toISOString()],
            'messages.setting_deleted'
        );
    }
}

