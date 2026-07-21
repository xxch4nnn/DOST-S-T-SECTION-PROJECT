<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Administrative Record') }}
        </h2>
        <a href="{{ route('admin-records.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Back to Directory
        </a>
    </div>
</x-slot>

<div class="py-12 relative">
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
        <div class="bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl p-6">
            <form wire:submit="save" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Record Type <span class="text-red-500">*</span></label>
                        <select wire:model="record_type" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            <option value="">Select type...</option>
                            @foreach($recordTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('record_type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Series / Reference Number</label>
                        <input wire:model="series_number" type="text" placeholder="e.g. Memo-2023-001" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('series_number') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Title / Subject <span class="text-red-500">*</span></label>
                        <input wire:model="title" type="text" placeholder="e.g. Guidelines on Scholarship Grant..." class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                        @error('title') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Recipient / Addressee</label>
                        <input wire:model="recipient" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('recipient') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Year</label>
                            <input wire:model="year" type="number" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('year') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quarter</label>
                            <select wire:model="quarter" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">N/A</option>
                                <option value="Q1">Q1</option>
                                <option value="Q2">Q2</option>
                                <option value="Q3">Q3</option>
                                <option value="Q4">Q4</option>
                            </select>
                            @error('quarter') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center md:col-span-2">
                        <label class="inline-flex items-center cursor-pointer">
                            <input wire:model="for_disposal" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                            <span class="ms-2 text-sm font-medium text-red-700">Flag for Disposal (Retention eligibility achieved)</span>
                        </label>
                        @error('for_disposal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-200/50">
                    <a href="{{ route('admin-records.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Create Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
