<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Settings</h2>
    </x-slot>

    @if (session('status'))
        <div class="mb-4 p-4 surface-muted text-gray-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="surface">
        <div class="p-6 text-gray-900">
                    <div class="text-lg font-medium mb-3">Deposit address pool (USDT BEP20)</div>

                    <form method="POST" action="{{ route('admin.settings.deposit_addresses.add') }}" class="flex gap-2 mb-4">
                        @csrf
                        <input name="address" placeholder="0x..." class="input px-3 py-2 w-full" />
                        <button class="btn-dark px-4 py-2 normal-case">Add</button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b border-slate-900/5">
                                    <th class="py-2 pr-4">ID</th>
                                    <th class="py-2 pr-4">Address</th>
                                    <th class="py-2 pr-4">Active</th>
                                    <th class="py-2 pr-4">Last assigned</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($addresses as $a)
                                    <tr class="border-b border-slate-900/5">
                                        <td class="py-2 pr-4">{{ $a->id }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs">{{ $a->address }}</td>
                                        <td class="py-2 pr-4">{{ $a->is_active ? 'yes' : 'no' }}</td>
                                        <td class="py-2 pr-4">{{ $a->last_assigned_at }}</td>
                                        <td class="py-2 pr-4">
                                            <form method="POST" action="{{ route('admin.settings.deposit_addresses.toggle', $a) }}">
                                                @csrf
                                                <button class="px-3 py-1 rounded-xl text-xs font-semibold {{ $a->is_active ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white' }}">
                                                    {{ $a->is_active ? 'Disable' : 'Enable' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        </div>
    </div>
</x-admin-layout>
