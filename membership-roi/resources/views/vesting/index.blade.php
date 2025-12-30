<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Vesting') }}</h2>
                <div class="text-sm text-gray-600">{{ __('QBIT token vesting and withdrawal schedule.') }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('wallet.index') }}" class="btn-neutral normal-case text-sm">{{ __('Wallet') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="surface p-6">
        <div class="text-lg font-medium text-gray-900 mb-2">{{ __('Vesting rules') }}</div>

        <div class="surface-muted p-4 text-sm text-gray-700 space-y-3">
            <div class="font-medium text-gray-900">{{ __('How it works') }}</div>
            <ul class="space-y-1">
                <li>- {{ __('QBIT tokens are dropped daily based on your QBP units and the current tier rate.') }}</li>
                <li>- {{ __('To start the vesting timer, click Vesting (this page) and then start vesting in the future release.') }}</li>
                <li>- {{ __('Withdrawable percentage depends on how long you wait after starting vesting.') }}</li>
            </ul>

            <div class="font-medium text-gray-900">{{ __('Withdrawal unlock') }}</div>
            <ul class="space-y-1">
                <li>- {{ __('After 30 days') }}: {{ __('25%') }}</li>
                <li>- {{ __('After 90 days') }}: {{ __('50%') }}</li>
                <li>- {{ __('After 180 days') }}: {{ __('100%') }}</li>
            </ul>
        </div>

        <div class="mt-4 text-xs text-gray-600">
            {{ __('Note: this page currently shows rules only; the on-chain/ledger vesting action is not enabled yet.') }}
        </div>
    </div>
</x-app-layout>

