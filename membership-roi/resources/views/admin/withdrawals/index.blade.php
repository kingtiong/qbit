<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Withdrawals</h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-muted text-gray-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="surface">
        <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-900/5">
                                <th class="py-2 pr-4">ID</th>
                                <th class="py-2 pr-4">User</th>
                                <th class="py-2 pr-4">Amount</th>
                                <th class="py-2 pr-4">Fee</th>
                                <th class="py-2 pr-4">Net</th>
                                <th class="py-2 pr-4">To</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2 pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($withdrawals as $w)
                                <tr class="border-b border-slate-900/5">
                                    <td class="py-2 pr-4">{{ $w->id }}</td>
                                    <td class="py-2 pr-4">{{ $w->user?->email }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $w->amount, 2) }}</td>
                                    <td class="py-2 pr-4">{{ $w->fee_type }} ({{ number_format((float) $w->fee_amount, 2) }})</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $w->net_amount, 2) }}</td>
                                    <td class="py-2 pr-4 font-mono text-xs">{{ $w->to_address }}</td>
                                    <td class="py-2 pr-4">{{ $w->status }}</td>
                                    <td class="py-2 pr-4">
                                        @if ($w->status === 'pending')
                                            <form method="POST" action="{{ route('admin.withdrawals.approve', $w) }}" class="inline">
                                                @csrf
                                                <button class="btn-primary px-3 py-1 normal-case text-xs">Approve</button>
                                            </form>
                                        @endif

                                        @if ($w->status === 'approved')
                                            <form method="POST" action="{{ route('admin.withdrawals.paid', $w) }}" class="inline">
                                                @csrf
                                                <input name="tx_hash" placeholder="tx hash" class="input px-2 py-1 text-xs w-40 align-middle" />
                                                <button class="btn-dark px-3 py-1 normal-case text-xs align-middle">Mark Paid</button>
                                            </form>
                                        @endif

                                        @if (in_array($w->status, ['pending','approved'], true))
                                            <form method="POST" action="{{ route('admin.withdrawals.reject', $w) }}" class="inline">
                                                @csrf
                                                <input name="admin_note" placeholder="reason" class="input px-2 py-1 text-xs w-40 align-middle" />
                                                <button class="btn-danger px-3 py-1 normal-case text-xs align-middle">Reject</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $withdrawals->links() }}</div>
        </div>
    </div>
</x-admin-layout>
