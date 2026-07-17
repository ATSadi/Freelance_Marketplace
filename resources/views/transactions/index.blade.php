<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Transactions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($user->role === \App\Models\User::ROLE_CLIENT)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-sm text-gray-500">{{ __('Currently in escrow') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-amber-600">${{ number_format($totalHeld, 2) }}</p>
                    </div>
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <p class="text-sm text-gray-500">{{ __('Total released to freelancers') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-green-600">${{ number_format($totalReleased, 2) }}</p>
                    </div>
                @else
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 sm:col-span-2">
                        <p class="text-sm text-gray-500">{{ __('Total earned') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-green-600">${{ number_format($totalEarned, 2) }}</p>
                    </div>
                @endif
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">{{ __('Transaction history') }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ __('Mock escrow ledger for milestone payments.') }}</p>

                    @if ($transactions->isEmpty())
                        <p class="mt-4 text-sm text-gray-600">{{ __('No transactions yet.') }}</p>
                    @else
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 text-left text-gray-500">
                                        <th class="py-2 pr-4 font-medium">{{ __('Date') }}</th>
                                        <th class="py-2 pr-4 font-medium">{{ __('Type') }}</th>
                                        <th class="py-2 pr-4 font-medium">{{ __('Project') }}</th>
                                        <th class="py-2 pr-4 font-medium">{{ __('Amount') }}</th>
                                        <th class="py-2 font-medium">{{ __('Details') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($transactions as $transaction)
                                        <tr>
                                            <td class="py-3 pr-4 whitespace-nowrap">{{ $transaction->created_at->format('M j, Y') }}</td>
                                            <td class="py-3 pr-4"><x-transaction-type-badge :type="$transaction->type" /></td>
                                            <td class="py-3 pr-4">
                                                <a href="{{ route('projects.show', $transaction->project) }}" class="text-indigo-600 hover:text-indigo-800">
                                                    {{ $transaction->project->title }}
                                                </a>
                                            </td>
                                            <td class="py-3 pr-4 font-medium">${{ number_format($transaction->amount, 2) }}</td>
                                            <td class="py-3 text-gray-600">{{ $transaction->description }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
