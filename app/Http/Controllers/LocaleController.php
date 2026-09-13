<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    public function update(string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('localization.supported')), 404);

        return redirect()->back()->withCookie(
            cookie()->forever(config('localization.cookie'), $locale)
        );
    }
}
