@props(['project', 'milestone' => null])

@php
    $milestone = $milestone ?? new \App\Models\Milestone();
@endphp

<div class="mb-4 rounded-md bg-indigo-50 border border-indigo-200 p-4 text-sm text-indigo-800">
    <p><span class="font-medium">{{ __('Agreed budget:') }}</span> ${{ number_format($project->agreedAmount(), 2) }}</p>
    <p><span class="font-medium">{{ __('Allocated so far:') }}</span> ${{ number_format($project->milestonesTotal(), 2) }}</p>
    <p><span class="font-medium">{{ __('Remaining:') }}</span> ${{ number_format($project->remainingBudget(), 2) }}</p>
</div>

<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
        :value="old('title', $milestone->title)" required />
    <x-input-error class="mt-2" :messages="$errors->get('title')" />
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" rows="3"
        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        required>{{ old('description', $milestone->description) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('description')" />
</div>

<div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <x-input-label for="amount" :value="__('Amount ($)')" />
        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full"
            :value="old('amount', $milestone->amount)" required />
        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
    </div>

    <div>
        <x-input-label for="due_date" :value="__('Due Date')" />
        <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
            :value="old('due_date', $milestone->due_date?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
    </div>

    <div>
        <x-input-label for="order_index" :value="__('Order')" />
        <x-text-input id="order_index" name="order_index" type="number" min="0" class="mt-1 block w-full"
            :value="old('order_index', $milestone->order_index)" />
        <x-input-error class="mt-2" :messages="$errors->get('order_index')" />
    </div>
</div>
