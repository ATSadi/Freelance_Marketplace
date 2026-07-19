@props(['project' => null])

@php
    $project = $project ?? new \App\Models\Project();
@endphp

<div>
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
        :value="old('title', $project->title)" required />
    <x-input-error class="mt-2" :messages="$errors->get('title')" />
</div>

<div class="mt-4">
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" rows="5"
        class="wv-input"
        required>{{ old('description', $project->description) }}</textarea>
    <x-input-error class="mt-2" :messages="$errors->get('description')" />
</div>

<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="budget_min" :value="__('Minimum Budget ($)')" />
        <x-text-input id="budget_min" name="budget_min" type="number" step="0.01" min="0" class="mt-1 block w-full"
            :value="old('budget_min', $project->budget_min)" required />
        <x-input-error class="mt-2" :messages="$errors->get('budget_min')" />
    </div>

    <div>
        <x-input-label for="budget_max" :value="__('Maximum Budget ($)')" />
        <x-text-input id="budget_max" name="budget_max" type="number" step="0.01" min="0" class="mt-1 block w-full"
            :value="old('budget_max', $project->budget_max)" required />
        <x-input-error class="mt-2" :messages="$errors->get('budget_max')" />
    </div>
</div>

<div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="deadline" :value="__('Deadline')" />
        <x-text-input id="deadline" name="deadline" type="date" class="mt-1 block w-full"
            :value="old('deadline', $project->deadline?->format('Y-m-d'))" required />
        <x-input-error class="mt-2" :messages="$errors->get('deadline')" />
    </div>

    <div>
        <x-input-label for="category" :value="__('Category')" />
        <x-text-input id="category" name="category" type="text" class="mt-1 block w-full"
            placeholder="{{ __('e.g. Web Development, Design') }}"
            :value="old('category', $project->category)" required />
        <x-input-error class="mt-2" :messages="$errors->get('category')" />
    </div>
</div>
