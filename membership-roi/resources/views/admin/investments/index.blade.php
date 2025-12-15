<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Investments</h2>
    </x-slot>

    <div class="surface">
        <div class="p-6 text-gray-900 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-900/5">
                        <th class="py-2 pr-4">ID</th>
                        <th class="py-2 pr-4">User</th>
                        <th class="py-2 pr-4">Package</th>
                        <th class="py-2 pr-4">Amount</th>
                        <th class="py-2 pr-4">Earned</th>
                        <th class="py-2 pr-4">Max</th>
                        <th class="py-2 pr-4">Status</th>
                        <th class="py-2 pr-4">Started</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($investments as $i)
                        <tr class="border-b border-slate-900/5">
                            <td class="py-2 pr-4">{{ $i->id }}</td>
                            <td class="py-2 pr-4">{{ $i->user?->email }}</td>
                            <td class="py-2 pr-4">{{ $i->package?->label }}</td>
                            <td class="py-2 pr-4">{{ number_format((float) $i->amount, 2) }}</td>
                            <td class="py-2 pr-4">{{ number_format((float) $i->total_earned, 2) }}</td>
                            <td class="py-2 pr-4">{{ number_format((float) $i->max_return_amount, 2) }}</td>
                            <td class="py-2 pr-4">{{ $i->status }}</td>
                            <td class="py-2 pr-4">{{ $i->started_on?->toDateString() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">{{ $investments->links() }}</div>
        </div>
    </div>
</x-admin-layout>
