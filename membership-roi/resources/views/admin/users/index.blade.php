<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Users</h2>
    </x-slot>

    <div class="page-section">
        <div class="page-container">
            <div class="surface">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-900/5">
                                <th class="py-2 pr-4">ID</th>
                                <th class="py-2 pr-4">Name</th>
                                <th class="py-2 pr-4">Email</th>
                                <th class="py-2 pr-4">Invite</th>
                                <th class="py-2 pr-4">Sponsor</th>
                                <th class="py-2 pr-4">Registered</th>
                                <th class="py-2 pr-4">Commission</th>
                                <th class="py-2 pr-4">Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $u)
                                <tr class="border-b border-slate-900/5">
                                    <td class="py-2 pr-4">{{ $u->id }}</td>
                                    <td class="py-2 pr-4">{{ $u->name }}</td>
                                    <td class="py-2 pr-4">{{ $u->email }}</td>
                                    <td class="py-2 pr-4 font-mono text-xs">{{ $u->invite_code }}</td>
                                    <td class="py-2 pr-4">{{ $u->sponsor_id ?? '—' }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) ($u->registeredWallet?->balance ?? 0), 2) }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) ($u->commissionWallet?->balance ?? 0), 2) }}</td>
                                    <td class="py-2 pr-4">{{ $u->is_admin ? 'yes' : 'no' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">{{ $users->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
