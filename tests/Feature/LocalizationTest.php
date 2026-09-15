<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_default_locale_is_english(): void
    {
        $this->get(route('home'))->assertSee('Practical knowledge.');
    }

    public function test_locale_switch_sets_cookie_and_redirects_back(): void
    {
        $response = $this->from(route('home'))->get(route('locale.update', 'ms'));

        $response->assertRedirect(route('home'));
        $response->assertCookie(config('localization.cookie'), 'ms');
    }

    public function test_locale_cookie_renders_malay_copy(): void
    {
        $this->withCookie(config('localization.cookie'), 'ms')
            ->get(route('home'))
            ->assertSee('Ilmu praktikal.');
    }

    public function test_unsupported_locale_is_rejected(): void
    {
        $this->get(route('locale.update', 'fr'))->assertNotFound();
    }
}
