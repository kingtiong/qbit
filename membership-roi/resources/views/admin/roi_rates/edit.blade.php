<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin: Daily ROI Rate') }}
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @if (session('status'))
    <div class="mb-4 p-4 rounded-2xl bg-amber-500/10 ring-1 ring-amber-300/20 text-amber-50">
                {{ session('status') }}
            </div>
        @endif

        <div class="surface">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <div class="text-sm text-gray-600">Set the ROI rate (percent) for a date. Allowed range: <span class="font-medium">0.01%</span> to <span class="font-medium">1.00%</span>.</div>
                </div>

                <form method="POST" action="{{ route('admin.roi_rates.upsert') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="date" :value="__('Date')" />
                        <x-text-input id="date" name="date" type="date" class="mt-1 block w-full" :value="old('date', $date->toDateString())" required />
                        <x-input-error class="mt-2" :messages="$errors->get('date')" />
                    </div>

                    <div>
                        <x-input-label for="rate_percent" :value="__('Rate (%)')" />
                        <x-text-input id="rate_percent" name="rate_percent" type="number" step="0.01" min="0.01" max="1.00" class="mt-1 block w-full" :value="old('rate_percent', $rate ? bcmul((string) $rate->rate, '100', 2) : '')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('rate_percent')" />
                    </div>

                    <div>
                        <x-input-label for="note" :value="__('Note (optional)')" />
                        <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" :value="old('note', $rate?->note)" />
                        <x-input-error class="mt-2" :messages="$errors->get('note')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>
                            {{ __('Save') }}
                        </x-primary-button>

                        @if ($rate)
                            <div class="text-sm text-gray-600">
                                Current stored: <span class="font-medium">{{ bcmul((string) $rate->rate, '100', 2) }}%</span>
                            </div>
                        @endif
                    </div>
                </form>

                <div class="mt-6 text-sm text-gray-600">
                    Tip: after setting rates, run the accrual job with:
                    <div class="mt-2 font-mono text-xs surface-muted p-2 overflow-x-auto">php artisan roi:accrue --date={{ $date->toDateString() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
