<div>
    <div class="mb-6">
        <h1 class="text-xl font-semibold text-gray-900">Audit Log</h1>
        <p class="text-sm text-gray-500 mt-1">Review recorded system actions, actors, and payload changes. Restricted to Admins.</p>
    </div>

    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                <div class="sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700">Action</label>
                    <input type="text" wire:model="action" class="mt-1 block w-full rounded-md border-gray-300 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700">Record Type</label>
                    <input type="text" wire:model="recordType" class="mt-1 block w-full rounded-md border-gray-300 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="sm:col-span-1">
                    <label class="block text-xs font-medium text-gray-700">User</label>
                    <input type="text" wire:model="user" class="mt-1 block w-full rounded-md border-gray-300 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">From</label>
                    <input type="date" wire:model="from" class="mt-1 block w-full rounded-md border-gray-300 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">To</label>
                    <input type="date" wire:model="to" class="mt-1 block w-full rounded-md border-gray-300 bg-white/50 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
                <div class="flex items-end">
                    <button wire:click="clear" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">Reset</button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">When</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">User</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Action</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Record</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Before</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">After</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="px-4 py-2 text-xs text-gray-700 whitespace-nowrap">{{ $log->created_at?->diffForHumans() }}</td>
                            <td class="px-4 py-2 text-xs text-gray-700">
                                <div class="font-medium text-gray-900">{{ $log->user?->name ?? 'System' }}</div>
                                <div class="text-gray-500">{{ $log->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-2 text-xs font-mono text-gray-700">{{ $log->action }}</td>
                            <td class="px-4 py-2 text-xs text-gray-700">
                                <div class="font-medium text-gray-900">{{ class_basename($log->record_type) }}</div>
                                <div class="text-gray-500">#{{ $log->record_id }}</div>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-600">
                                <pre class="whitespace-pre-wrap break-words max-h-24 overflow-auto">{{ json_encode($log->before_payload) }}</pre>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-600">
                                <pre class="whitespace-pre-wrap break-words max-h-24 overflow-auto">{{ json_encode($log->after_payload) }}</pre>
                            </td>
                            <td class="px-4 py-2 text-xs text-gray-500 whitespace-nowrap">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">No audit entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>
</div>
