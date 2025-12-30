<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Network') }}</h2>
                <div class="text-sm text-gray-600">{{ __('Invite members and build your network.') }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('qbp.index') }}" class="btn-neutral normal-case text-sm">{{ __('QBP') }}</a>
                <a href="{{ route('dashboard') }}" class="btn-neutral normal-case text-sm">{{ __('Dashboard') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="surface p-6">
        <div class="text-sm text-gray-600">{{ __('Your invitation link') }}</div>
        <div
            class="mt-2"
            x-data="{
                link: @js($inviteLink),
                copied: false,
                async copy() {
                    try {
                        if (navigator?.clipboard?.writeText) {
                            await navigator.clipboard.writeText(this.link);
                        } else {
                            const ta = document.createElement('textarea');
                            ta.value = this.link;
                            ta.style.position = 'fixed';
                            ta.style.left = '-9999px';
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            document.body.removeChild(ta);
                        }
                        this.copied = true;
                        setTimeout(() => (this.copied = false), 1500);
                    } catch (e) {
                        // ignore
                    }
                },
            }"
        >
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-block font-mono text-xs bg-black/60 text-amber-50 px-3 py-2 rounded-xl ring-1 ring-amber-300/25 break-all">
                    {{ $inviteLink }}
                </span>
                <button type="button" class="btn-primary normal-case text-xs px-3 py-2" @click="copy()">
                    <span x-show="!copied">{{ __('Copy') }}</span>
                    <span x-show="copied" x-cloak>{{ __('Copied') }}</span>
                </button>
            </div>
        </div>

        <div class="mt-6">
            <div class="text-lg font-medium text-gray-900">{{ __('Direct referrals') }}</div>
            <div class="text-sm text-gray-600 mb-3">
                {{ __('Count') }}: <span class="font-semibold text-amber-50">{{ $referrals->count() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-slate-900/5">
                            <th class="py-2 pr-4">{{ __('Name') }}</th>
                            <th class="py-2 pr-4">{{ __('Email') }}</th>
                            <th class="py-2 pr-4">{{ __('Joined') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($referrals as $r)
                            <tr class="border-b border-slate-900/5">
                                <td class="py-2 pr-4 font-medium">{{ $r->name }}</td>
                                <td class="py-2 pr-4">{{ $r->email }}</td>
                                <td class="py-2 pr-4">{{ $r->created_at?->toDateString() ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-3 text-gray-600" colspan="3">{{ __('No referrals yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

