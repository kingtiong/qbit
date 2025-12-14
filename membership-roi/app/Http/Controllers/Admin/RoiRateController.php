<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoiRate;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RoiRateController extends Controller
{
    public function edit(Request $request): View
    {
        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->startOfDay()
            : Carbon::today();

        $rate = RoiRate::query()->whereDate('date', $date)->first();

        return view('admin.roi_rates.edit', [
            'date' => $date,
            'rate' => $rate,
        ]);
    }

    public function upsert(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            // Admin enters percent value between 0.5 and 0.8 (inclusive).
            'rate_percent' => ['required', 'numeric', 'min:0.5', 'max:0.8'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->startOfDay();
        $fractionalRate = ((float) $validated['rate_percent']) / 100.0;

        RoiRate::updateOrCreate(
            ['date' => $date->toDateString()],
            [
                'rate' => $fractionalRate,
                'set_by_user_id' => Auth::id(),
                'note' => $validated['note'] ?? null,
            ],
        );

        return redirect()
            ->route('admin.roi_rates.edit', ['date' => $date->toDateString()])
            ->with('status', 'ROI rate saved.');
    }
}
