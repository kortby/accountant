<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AiController extends Controller
{
    /**
     * Show the AI settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Ai', [
            'aiEnabled' => (bool) $request->user()->ai_enabled,
        ]);
    }

    /**
     * Update the AI settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ai_enabled' => ['required', 'boolean'],
        ]);

        $request->user()->update([
            'ai_enabled' => $validated['ai_enabled'],
        ]);

        return back();
    }
}
