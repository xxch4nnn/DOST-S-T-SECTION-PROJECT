<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('DOSTorage Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 relative">
        <!-- Glassmorphism BG Circles -->
        <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden">
            <div class="absolute top-10 left-10 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
        </div>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 relative z-10 space-y-6">
            
            <!-- Welcome Header -->
            <div class="bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl p-8">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                    Welcome to DOSTorage, {{ auth()->user()->name }}!
                </h1>
                <p class="mt-2 text-md text-gray-600 max-w-2xl">
                    Digitized records portal for the DOST Region XI Scholarship Section. Manage physical 201 files and administrative documents offline securely.
                </p>
            </div>

            <!-- Quick Access Modules -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Scholar 201 module link -->
                <a href="{{ route('scholars.index') }}" class="block group bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl p-8 hover:bg-white/80 transition-all duration-300 hover:shadow-2xl">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-blue-100 rounded-lg group-hover:bg-blue-200 transition-colors">
                            <!-- Icon -->
                            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Scholar 201 Registry</h2>
                            <p class="mt-1 text-sm text-gray-500">Search scholars, view files, and upload scanned documents.</p>
                        </div>
                    </div>
                </a>

                <!-- Administrative Records link -->
                <a href="{{ route('admin-records.index') }}" class="block group bg-white/70 backdrop-blur-xl border border-white/50 overflow-hidden shadow-xl sm:rounded-2xl p-8 hover:bg-white/80 transition-all duration-300 hover:shadow-2xl">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-purple-100 rounded-lg group-hover:bg-purple-200 transition-colors">
                            <!-- Icon -->
                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Administrative Records</h2>
                            <p class="mt-1 text-sm text-gray-500">Manage official documents, memoranda, special orders, and reports.</p>
                        </div>
                    </div>
                </a>

            </div>

        </div>
    </div>
</x-app-layout>
