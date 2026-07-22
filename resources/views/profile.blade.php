<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0 fw-semibold">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
