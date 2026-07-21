<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administrative Records') }}
        </h2>
        <a href="{{ route('admin-records.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
            Add Admin Record
        </a>
    </div>
</x-slot>

<div class="py-12 relative">
    <!-- Background decorations for glassmorphism -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute top-0 right-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
        
        <!-- Filters Card -->
        <div class="bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl mb-6">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search Title / Series / Recipient</label>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 backdrop-blur-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Search...">
                    </div>
                    <div>
                        <label for="record_type" class="block text-sm font-medium text-gray-700">Record Type</label>
                        <select wire:model.live="record_type" id="record_type" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 backdrop-blur-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Types</option>
                            @foreach($recordTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                        <input wire:model.live.debounce.300ms="year" type="number" id="year" placeholder="e.g. 2023" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 backdrop-blur-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table Card -->
        <div class="bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200/50">
                    <thead class="bg-gray-50/50 backdrop-blur-md">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type & Series</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recipient</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year/Quarter</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-gray-200/50">
                        @forelse ($records as $record)
                            <tr class="hover:bg-white/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="font-semibold">{{ $record->record_type }}</div>
                                    <div class="text-xs text-gray-500">{{ $record->series_number ?? 'No Series' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-900 max-w-xs">
                                    {{ $record->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $record->recipient ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $record->year ?? 'N/A' }} {{ $record->quarter ? "($record->quarter)" : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin-records.show', $record->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    <a href="{{ route('admin-records.edit', $record->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No administrative records found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200/50 bg-gray-50/30">
                {{ $records->links() }}
            </div>
        </div>

    </div>
</div>
