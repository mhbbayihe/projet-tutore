<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

// Définissez le layout pour cette page
new #[Layout('components.layouts.app')] class extends Component
{
    // ... (pas de logique spécifique ici pour le moment) ...
};
?>

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Gestion des Communautés (Admin)') }}
    </h2>
</x-slot>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed bottom-4 right-4 bg-red-500 text-white p-4 rounded-lg shadow-lg z-50">
                {{ session('error') }}
            </div>
        @endif

        <livewire:community-list /> {{-- Le composant Livewire CommunityList ne change pas de chemin --}}
    </div>
</div>