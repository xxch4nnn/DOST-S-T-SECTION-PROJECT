<div class="notifications-container">
    <div class="notifications-card">
        
        {{-- Header Section --}}
        <div class="notifications-header">
            <div class="notifications-title-row">
                <h1 class="notifications-title">Notifications</h1>
                @if($unreadCount > 0)
                    <span class="notifications-count-badge" aria-label="{{ $unreadCount }} unread notifications">
                        {{ $unreadCount }}
                    </span>
                @endif
            </div>

            {{-- Action Bar: Filter Pills & Mark All Read --}}
            <div class="notifications-action-bar">
                <div class="notifications-filters">
                    <button type="button"
                            wire:click="setFilter('all')"
                            class="filter-pill {{ $filter === 'all' ? 'filter-pill--active' : 'filter-pill--inactive' }}">
                        All
                    </button>

                    <button type="button"
                            wire:click="setFilter('unread')"
                            class="filter-pill {{ $filter === 'unread' ? 'filter-pill--active' : 'filter-pill--inactive' }}">
                        Unread ({{ $unreadCount }})
                    </button>
                </div>

                <div>
                    <button type="button"
                            wire:click="markAllAsRead"
                            class="mark-all-read-btn"
                            @if($unreadCount === 0) disabled @endif>
                        Mark all as read
                    </button>
                </div>
            </div>
        </div>

        {{-- Notifications List --}}
        <div class="notifications-list" role="list">
            @forelse($filteredNotifications as $notif)
                <div wire:key="notif-{{ $notif['id'] }}"
                     wire:click="markAsRead('{{ $notif['id'] }}')"
                     class="notification-item {{ !empty($notif['is_unread']) ? 'notification-item--unread' : 'notification-item--read' }}"
                     role="listitem"
                     tabindex="0"
                     aria-label="Notification: {{ $notif['actor'] }} {{ $notif['action_text'] }} {{ $notif['target_name'] }}">
                    
                    <div class="notification-content-wrap">
                        {{-- Red Unread Dot or Placeholder for Alignment --}}
                        @if(!empty($notif['is_unread']))
                            <span class="unread-indicator" title="Unread"></span>
                        @else
                            <span class="unread-indicator-placeholder" aria-hidden="true"></span>
                        @endif

                        {{-- Notification Text Content --}}
                        <div class="notification-text">
                            <strong class="notification-actor">{{ $notif['actor'] }}</strong>
                            <span>{{ $notif['action_text'] }}</span>
                            @if(!empty($notif['target_name']))
                                <strong class="notification-target">{{ $notif['target_name'] }}</strong>
                            @endif
                        </div>
                    </div>

                    {{-- Relative Timestamp --}}
                    <div class="notification-time">
                        {{ $notif['time_ago'] }}
                    </div>
                </div>
            @empty
                <div class="notification-empty-state">
                    <i class="ph ph-bell-slash empty-icon"></i>
                    <h4>No {{ $filter === 'unread' ? 'unread ' : '' }}notifications</h4>
                    <p>You're all caught up! When updates or edits occur, they will appear here.</p>
                </div>
            @endforelse
        </div>

        {{-- Interactive Live Corner Alert Test Bar (Demonstrates Image 2 alert themes) --}}
        <div class="toast-demo-bar">
            <span class="demo-label">Test Corner Alerts:</span>
            
            <button type="button" 
                    wire:click="triggerDemoAlert('purple', 'Admin Name edited document metadata for Fernandez, Gianfranco Miguel D.')"
                    class="demo-btn toast-purple">
                <i class="ph ph-warning me-1"></i> Purple Alert
            </button>

            <button type="button" 
                    wire:click="triggerDemoAlert('cyan', 'New clearance document uploaded by Scholar Staff.')"
                    class="demo-btn toast-cyan">
                <i class="ph ph-warning me-1"></i> Cyan Alert
            </button>

            <button type="button" 
                    wire:click="triggerDemoAlert('green', 'A simple content alert—check it out!')"
                    class="demo-btn toast-green">
                <i class="ph ph-warning me-1"></i> Green Alert
            </button>

            <button type="button" 
                    wire:click="triggerDemoAlert('yellow', 'Document pending signature verification.')"
                    class="demo-btn toast-yellow">
                <i class="ph ph-warning me-1"></i> Yellow Alert
            </button>

            <button type="button" 
                    wire:click="triggerDemoAlert('red', 'Warning: Clearance status is currently Not Cleared.')"
                    class="demo-btn toast-red">
                <i class="ph ph-warning me-1"></i> Red Alert
            </button>

            <button type="button" 
                    wire:click="triggerDemoAlert('gray', 'System maintenance scheduled at 10:00 PM.')"
                    class="demo-btn toast-gray">
                <i class="ph ph-warning me-1"></i> Gray Alert
            </button>
        </div>

    </div>
</div>
