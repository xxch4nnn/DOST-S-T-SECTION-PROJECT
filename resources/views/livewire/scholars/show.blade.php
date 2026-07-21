<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $scholar->first_name }} {{ $scholar->last_name }}'s Record
        </h2>
        <a href="{{ route('scholars.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Back to Directory
        </a>
    </div>
</x-slot>

<div class="py-12 relative">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Scholar Details -->
        <div class="col-span-1 bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Scholar Information</h3>
                    <a href="{{ route('scholars.edit', $scholar->id) }}" class="text-xs font-semibold px-2 py-1 bg-indigo-555 text-blue-600 border border-blue-500 rounded hover:bg-blue-50">Edit Info</a>
                </div>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">SPAS No.</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $scholar->spas_no ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $scholar->first_name }} {{ $scholar->middle_name }} {{ $scholar->last_name }} {{ $scholar->generational_suffix }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Scholarship</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $scholar->scholarship?->name }} ({{ $scholar->year_of_award }})</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">School & Course</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $scholar->school?->name }} - {{ $scholar->course?->abbreviation }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sex & Birthdate</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $scholar->sex ?? 'N/A' }} / {{ $scholar->birthdate ? $scholar->birthdate->format('M d, Y') : 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $scholar->contact_number ?? 'N/A' }} <br> {{ $scholar->email_address ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Geographic Address</dt>
                        <dd class="mt-1 text-sm text-gray-500">
                            {{ $scholar->barangay ? $scholar->barangay . ', ' : '' }}
                            {{ $scholar->municipality ? $scholar->municipality . ', ' : '' }}
                            {{ $scholar->province ? $scholar->province : '' }}
                            <div class="text-xs text-gray-400">Region: {{ $scholar->region?->abbreviation }}</div>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Clearance Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $scholar->clearanceStatus?->name ?? 'Active' }}
                            </span>
                            @if($scholar->clearance_date)
                                <div class="text-xs text-gray-400 mt-1">Cleared: {{ $scholar->clearance_date->format('M d, Y') }}</div>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Disposal Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($scholar->for_disposal)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Eligible for Disposal</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Retained</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Documents Section -->
        <div class="col-span-1 md:col-span-2 flex flex-col gap-6">
            
            <!-- Upload Document -->
            <div class="bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Upload New Document</h3>
                
                @if (session()->has('message'))
                    <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit="uploadDocument" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Type <span class="text-red-500">*</span></label>
                            <select wire:model="file_type_id" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Select type...</option>
                                @foreach($fileTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->year }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">File <span class="text-red-500">*</span></label>
                            <input wire:model="file" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                            <div wire:loading wire:target="file" class="text-xs text-blue-500 mt-1">Uploading...</div>
                            @error('file') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" wire:loading.attr="disabled">
                            Upload
                        </button>
                    </div>
                </form>
            </div>

            <!-- Document List -->
            <div class="bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 border-b border-gray-200/50 bg-gray-50/30">
                    <h3 class="text-lg font-medium text-gray-900">Document History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200/50">
                        <thead class="bg-gray-50/50 backdrop-blur-md">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-transparent divide-y divide-gray-200/50">
                            @forelse ($documents as $doc)
                                <tr class="hover:bg-white/40 transition-colors {{ $doc->trashed() ? 'opacity-50 bg-red-50/20' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $doc->fileType?->name }}
                                        @if($doc->trashed())
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                Struck Off
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ Str::limit($doc->original_filename, 30) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $doc->file_size_kb }} KB
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $doc->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if(!$doc->trashed())
                                            <a href="{{ route('documents.download', $doc->id) }}" class="text-blue-600 hover:text-blue-900 mr-3" target="_blank">Download</a>
                                            @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                                                <button wire:click="strikeOff({{ $doc->id }})" wire:confirm="Are you sure you want to strike off this document? This soft-delete can be undone." class="text-red-600 hover:text-red-950">Strike Off</button>
                                            @endif
                                        @else
                                            @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                                                <button wire:click="undoStrikeOff({{ $doc->id }})" class="text-green-600 hover:text-green-950">Undo Strike Off</button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        No documents uploaded yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Duplicate Modal (Priority 2 Step 6) -->
@if($showDuplicateModal)
    <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-lg max-w-md w-full p-6 shadow-2xl border border-gray-100">
            <h4 class="text-lg font-bold text-gray-900 mb-2">Duplicate Document Detected</h4>
            <p class="text-sm text-gray-600 mb-4">
                An active document of type <strong>{{ $duplicateDocument->fileType?->name }}</strong> already exists for this record:
                <br><span class="text-xs text-gray-500">Existing file: {{ $duplicateDocument->original_filename }} (Uploaded on {{ $duplicateDocument->created_at->format('M d, Y') }})</span>
            </p>
            <div class="flex flex-col gap-2">
                <button wire:click="resolveDuplicate('keep_history')" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-semibold">
                    Keep History (Archive current, upload new as active)
                </button>
                <button wire:click="resolveDuplicate('overwrite')" class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded text-sm font-semibold">
                    Overwrite (Physically delete current, replace metadata)
                </button>
                <button wire:click="resolveDuplicate('cancel')" class="w-full px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded text-sm font-semibold">
                    Cancel (Keep current file untouched)
                </button>
            </div>
        </div>
    </div>
@endif
