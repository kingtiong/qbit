<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Investment Packages') }}
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
                <div class="p-6 text-gray-900">
                    <div class="flex items-baseline justify-between gap-4 flex-wrap">
                        <div>
                            <div class="text-lg font-medium">Today's ROI rate</div>
                            <div class="text-sm text-gray-600">
                                {{ $today->toDateString() }}
                            </div>
                        </div>
                        <div class="text-2xl font-semibold">
                            @if ($todayRate)
                                {{ bcmul((string) $todayRate->rate, '100', 2) }}%
                            @else
                                <span class="text-gray-500">Not set</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">
                        Admin sets a daily ROI rate between <span class="font-medium">0.5%</span> and <span class="font-medium">0.8%</span>.
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2 pr-4">Package</th>
                                    <th class="py-2 pr-4">Amount</th>
                                    <th class="py-2 pr-4">Status</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($packages as $package)
                                    <tr class="border-b">
                                        <td class="py-3 pr-4 font-medium">{{ $package->label }}</td>
                                        <td class="py-3 pr-4">{{ $package->currency }} {{ number_format((float) $package->amount, 2) }}</td>
                                        <td class="py-3 pr-4">
                                            @if ($package->is_active)
                                                <span class="inline-flex items-center px-2 py-1 rounded bg-green-50 text-green-700 border border-green-200">Active</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded bg-gray-50 text-gray-700 border border-gray-200">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4">
                                            <form method="POST" action="{{ route('investments.store') }}">
                                                @csrf
                                                <input type="hidden" name="investment_package_id" value="{{ $package->id }}" />
                                                <x-primary-button :disabled="!$package->is_active">
                                                    {{ __('Invest') }}
                                                </x-primary-button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($packages->isEmpty())
                        <div class="text-sm text-gray-600">No packages configured.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
