<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Scholar') }}
        </h2>
        <a href="{{ route('scholars.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
            &larr; Back to Directory
        </a>
    </div>
</x-slot>

<div class="py-12 relative">
    <!-- Background decorations for glassmorphism -->
    <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10">
        <div class="bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl p-6">
            <form wire:submit="save" class="space-y-8">
                <!-- Section 1: Personal Information -->
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 border-b border-gray-200/50 pb-2">1. Personal Information</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
                            <input wire:model="first_name" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('first_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Middle Name</label>
                            <input wire:model="middle_name" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('middle_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
                            <input wire:model="last_name" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('last_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Suffix (e.g. Jr, III)</label>
                            <input wire:model="generational_suffix" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('generational_suffix') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Sex</label>
                            <select wire:model="sex" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            @error('sex') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Birthdate</label>
                            <input wire:model="birthdate" type="date" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('birthdate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Contact Number</label>
                            <input wire:model="contact_number" type="text" placeholder="09171234567" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('contact_number') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input wire:model="email_address" type="email" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('email_address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Scholarship & Academic Info -->
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 border-b border-gray-200/50 pb-2">2. Scholarship & Academic Information</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SPAS No.</label>
                            <input wire:model="spas_no" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('spas_no') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Year of Award <span class="text-red-500">*</span></label>
                            <input wire:model="year_of_award" type="number" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                            @error('year_of_award') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Scholarship <span class="text-red-500">*</span></label>
                            <select wire:model="scholarship_id" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Select...</option>
                                @foreach($scholarships as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            @error('scholarship_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Scholarship Type <span class="text-red-500">*</span></label>
                            <select wire:model="scholarship_type_id" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Select...</option>
                                @foreach($scholarshipTypes as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                            @error('scholarship_type_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">School <span class="text-red-500">*</span></label>
                            <select wire:model="school_id" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Select...</option>
                                @foreach($schools as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->campus }})</option>
                                @endforeach
                            </select>
                            @error('school_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Course</label>
                            <select wire:model="course_id" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">Select...</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->abbreviation }})</option>
                                @endforeach
                            </select>
                            @error('course_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Program / Major / Specialization Details</label>
                            <input wire:model="program" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('program') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Address & Geographic Info -->
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 border-b border-gray-200/50 pb-2">3. Address & Geographic Information</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barangay</label>
                            <input wire:model="barangay" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('barangay') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Municipality / City</label>
                            <input wire:model="municipality" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('municipality') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">District</label>
                            <input wire:model="district" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('district') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Province</label>
                            <input wire:model="province" type="text" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('province') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Region <span class="text-red-500">*</span></label>
                            <select wire:model="region_id" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                <option value="">Select...</option>
                                @foreach($regions as $r)
                                    <option value="{{ $r->id }}">{{ $r->name }} ({{ $r->abbreviation }})</option>
                                @endforeach
                            </select>
                            @error('region_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 4: Clearance & Disposal -->
                <div>
                    <h3 class="text-lg font-medium leading-6 text-gray-900 border-b border-gray-200/50 pb-2">4. Clearance & Status</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Clearance Status <span class="text-red-500">*</span></label>
                            <select wire:model="clearance_status_id" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" required>
                                @foreach($clearanceStatuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                            @error('clearance_status_id') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Clearance Date</label>
                            <input wire:model="clearance_date" type="date" class="mt-1 block w-full rounded-md border-gray-300/50 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            @error('clearance_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center mt-6">
                            <label class="inline-flex items-center cursor-pointer">
                                <input wire:model="for_disposal" type="checkbox" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                <span class="ms-2 text-sm font-medium text-red-700">Flag for Disposal (Retention eligibility achieved)</span>
                            </label>
                            @error('for_disposal') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-gray-200/50">
                    <a href="{{ route('scholars.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Scholar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
