<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaAssetsTest extends TestCase
{
    public function test_manifest_is_valid_and_has_required_fields(): void
    {
        $this->assertFileExists(public_path('manifest.webmanifest'));
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        $this->assertIsArray($manifest);
        foreach (['name', 'short_name', 'start_url', 'scope', 'display', 'background_color', 'theme_color', 'icons'] as $key) {
            $this->assertArrayHasKey($key, $manifest);
        }

        $sizes = collect($manifest['icons'])->pluck('sizes')->all();
        $this->assertContains('192x192', $sizes);
        $this->assertContains('512x512', $sizes);
        $this->assertTrue(collect($manifest['icons'])->contains(fn ($icon) => ($icon['purpose'] ?? null) === 'maskable'));
    }

    public function test_manifest_icons_exist_on_disk(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true);

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists(public_path(ltrim($icon['src'], '/')));
        }
    }

    public function test_service_worker_and_offline_page_exist(): void
    {
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('offline.html'));
    }
}
