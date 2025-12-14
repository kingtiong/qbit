<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin: Settings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-gray-50 border border-gray-200 text-gray-800 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-lg font-medium mb-3">Deposit address pool (USDT BEP20)</div>

                    <form method="POST" action="{{ route('admin.settings.deposit_addresses.add') }}" class="flex gap-2 mb-4">
                        @csrf
                        <input name="address" placeholder="0x..." class="border rounded px-3 py-2 w-full" />
                        <button class="px-4 py-2 bg-gray-900 text-white rounded">Add</button>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2 pr-4">ID</th>
                                    <th class="py-2 pr-4">Address</th>
                                    <th class="py-2 pr-4">Active</th>
                                    <th class="py-2 pr-4">Last assigned</th>
                                    <th class="py-2 pr-4"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($addresses as $a)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">{{ $a->id }}</td>
                                        <td class="py-2 pr-4 font-mono text-xs">{{ $a->address }}</td>
                                        <td class="py-2 pr-4">{{ $a->is_active ? 'yes' : 'no' }}</td>
                                        <td class="py-2 pr-4">{{ $a->last_assigned_at }}</td>
                                        <td class="py-2 pr-4">
                                            <form method="POST" action="{{ route('admin.settings.deposit_addresses.toggle', $a) }}">
                                                @csrf
                                                <button class="px-3 py-1 rounded text-xs {{ $a->is_active ? 'bg-red-600 text-white' : 'bg-green-600 text-white' }}">
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
        </div>
    </div>
</x-app-layout>
