<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-sm text-gray-600">{{ __('Wallet balance') }}</div>
                        <div class="text-2xl font-semibold">
                            USD {{ number_format((float) $wallet->balance, 2) }}
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            Your invitation link:
                            <span class="font-mono">{{ url('/invite/'.auth()->user()->invite_code) }}</span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('packages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            {{ __('View packages') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium mb-4">{{ __('My investments') }}</div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b">
                                        <th class="py-2 pr-4">Package</th>
                                        <th class="py-2 pr-4">Amount</th>
                                        <th class="py-2 pr-4">Status</th>
                                        <th class="py-2 pr-4">Earned</th>
                                        <th class="py-2 pr-4">Last accrued</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($investments as $inv)
                                        <tr class="border-b">
                                            <td class="py-3 pr-4 font-medium">{{ $inv->package?->label ?? ('#'.$inv->investment_package_id) }}</td>
                                            <td class="py-3 pr-4">{{ $inv->currency }} {{ number_format((float) $inv->amount, 2) }}</td>
                                            <td class="py-3 pr-4">{{ $inv->status }}</td>
                                            <td class="py-3 pr-4">{{ $inv->currency }} {{ number_format((float) $inv->total_earned, 2) }}</td>
                                            <td class="py-3 pr-4">{{ $inv->last_accrued_on?->toDateString() ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="py-3 text-gray-600" colspan="5">No investments yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-lg font-medium mb-4">{{ __('Recent earnings / transactions') }}</div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-left border-b">
                                        <th class="py-2 pr-4">Date</th>
                                        <th class="py-2 pr-4">Type</th>
                                        <th class="py-2 pr-4">Amount</th>
                                        <th class="py-2 pr-4">Meta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentTransactions as $t)
                                        <tr class="border-b">
                                            <td class="py-3 pr-4">{{ $t->occurred_on->toDateString() }}</td>
                                            <td class="py-3 pr-4">{{ $t->type }}</td>
                                            <td class="py-3 pr-4">{{ number_format((float) $t->amount, 2) }}</td>
                                            <td class="py-3 pr-4 text-gray-600">{{ is_array($t->meta) ? json_encode($t->meta) : '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="py-3 text-gray-600" colspan="4">No transactions yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
