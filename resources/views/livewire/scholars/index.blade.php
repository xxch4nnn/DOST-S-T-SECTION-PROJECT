<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Scholars Directory') }}
        </h2>
        <a href="{{ route('scholars.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
            Add Scholar
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
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search Name</label>
                        <input wire:model.live.debounce.300ms="search" type="text" id="search" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 backdrop-blur-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Search first, middle, last name...">
                    </div>
                    <div>
                        <label for="spas_no" class="block text-sm font-medium text-gray-700">SPAS No.</label>
                        <input wire:model.live.debounce.300ms="spas_no" type="text" id="spas_no" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 backdrop-blur-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="e.g. 2023-0001">
                    </div>
                    <div>
                        <label for="school" class="block text-sm font-medium text-gray-700">School</label>
                        <select wire:model.live="school_id" id="school" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 backdrop-blur-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Schools</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }} ({{ $school->campus }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                        <select wire:model.live="course_id" id="course" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 backdrop-blur-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->abbreviation }}</option>
                            @endforeach
                        </select>
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SPAS No.</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">School & Course</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent divide-y divide-gray-200/50">
                        @forelse ($scholars as $scholar)
                            <tr class="hover:bg-white/40 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $scholar->spas_no }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="font-semibold">{{ $scholar->last_name }}, {{ $scholar->first_name }} {{ $scholar->middle_name }} {{ $scholar->generational_suffix }}</div>
                                    <div class="text-xs text-gray-500">{{ $scholar->scholarship?->name }} - {{ $scholar->year_of_award }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="text-gray-900">{{ $scholar->school?->name }}</div>
                                    <div>{{ $scholar->course?->abbreviation }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $scholar->clearanceStatus?->name ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('scholars.show', $scholar->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    <a href="{{ route('scholars.edit', $scholar->id) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No scholars found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200/50 bg-gray-50/30">
                {{ $scholars->links() }}
            </div>
        </div>

    </div>
</div>
