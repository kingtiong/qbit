<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Deposits</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b">
                                <th class="py-2 pr-4">ID</th>
                                <th class="py-2 pr-4">User</th>
                                <th class="py-2 pr-4">Amount</th>
                                <th class="py-2 pr-4">Status</th>
                                <th class="py-2 pr-4">To</th>
                                <th class="py-2 pr-4">Tx</th>
                                <th class="py-2 pr-4">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deposits as $d)
                                <tr class="border-b">
                                    <td class="py-2 pr-4">{{ $d->id }}</td>
                                    <td class="py-2 pr-4">{{ $d->user?->email }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $d->amount, 2) }}</td>
                                    <td class="py-2 pr-4">{{ $d->status }}</td>
                                    <td class="py-2 pr-4 font-mono text-xs">{{ $d->to_address }}</td>
                                    <td class="py-2 pr-4 font-mono text-xs">{{ $d->tx_hash }}</td>
                                    <td class="py-2 pr-4">{{ $d->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $deposits->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
