<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transactions') }}
            </h2>
            <a href="{{ route('transactions.create') }}" class="bg-blue-500 hover:bg-blue-700 justify-end text-white font-bold py-2 px-4 rounded float-right">Create Transaction</a>
        </div>


    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <x-flash-message :messages="session('messages')" class="mt-2" />
                <div class="overflow-x-auto flex flex-col p-6 shadow-md">
                    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-lg px-4 py-2">
                        <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Email</th>
                            <th class="py-3 px-6 text-left">Amount</th>
                            <th class="py-3 px-6 text-center">Reference</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-center">Type</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 text-sm font-light">
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td class="py-3 px-6 text-left">{{ $transaction->user->email }}</td>
                                <td class="py-3 px-6 text-left">{{ $transaction->amount }}</td>
                                <td class="py-3 px-6 text-left">{{ $transaction->reference }}</td>
                                <td class="py-3 px-6 text-left font-semibold">{{ strtoupper($transaction->status) }}</td>
                                <td class="py-3 px-6 text-left font-semibold">
                                    {{ strtoupper($transaction->type) }}
                                </td>
                                <td>
                                    @if ($transaction->status === 'pending' && auth()->user()->hasRole('checker'))
                                        <div class="flex justify-between">
                                        <form action="{{ route('transactions.approve', $transaction->id) }}" method="post">
                                            @csrf
                                            <button class="bg-gray-500 hover:bg-green-700 text-white font-bold py-2 px-2 rounded" type="submit">Approve</button>
                                        </form>
                                        <form action="{{ route('transactions.reject', $transaction->id) }}" method="post">
                                            @csrf
                                            <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-2 rounded" type="submit">Reject</button>
                                        </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
</x-app-layout>
