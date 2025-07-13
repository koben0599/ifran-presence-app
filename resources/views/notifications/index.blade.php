@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        <div class="flex space-x-2">
            <button id="markAllRead" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                <i class="fas fa-check-double mr-2"></i>Tout marquer comme lu
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            @if(count($notifications) > 0)
                <div class="space-y-4">
                    @foreach($notifications as $notification)
                        <div class="notification-item border rounded-lg p-4 {{ $notification['css_class'] ?? 'bg-gray-50 border-gray-200' }} {{ $notification['lu'] ? 'opacity-75' : '' }}" 
                             data-id="{{ $notification['id'] }}">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3 flex-1">
                                    <div class="flex-shrink-0">
                                        <i class="{{ $notification['icon'] ?? 'fas fa-bell' }} text-lg"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900">
                                            {{ $notification['titre'] }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $notification['message'] }}
                                        </p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs text-gray-500">
                                                <i class="far fa-clock mr-1"></i>
                                                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
                                            </span>
                                            @if($notification['action_url'])
                                                <a href="{{ $notification['action_url'] }}" 
                                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                    {{ $notification['action_text'] ?? 'Voir plus' }}
                                                    <i class="fas fa-arrow-right ml-1"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if(!$notification['lu'])
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Nouveau
                                        </span>
                                    @endif
                                    <button class="mark-read-btn text-gray-400 hover:text-gray-600" 
                                            data-id="{{ $notification['id'] }}"
                                            title="Marquer comme lu">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="delete-notification-btn text-gray-400 hover:text-red-600" 
                                            data-id="{{ $notification['id'] }}"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-bell-slash text-6xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune notification</h3>
                    <p class="text-gray-600">Vous n'avez aucune notification pour le moment.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marquer une notification comme lue
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            const notificationItem = this.closest('.notification-item');
            
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    notificationItem.classList.add('opacity-75');
                    const newBadge = notificationItem.querySelector('.bg-red-100');
                    if (newBadge) {
                        newBadge.remove();
                    }
                    updateNotificationCount();
                }
            });
        });
    });

    // Marquer toutes les notifications comme lues
    document.getElementById('markAllRead').addEventListener('click', function() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.add('opacity-75');
                    const newBadge = item.querySelector('.bg-red-100');
                    if (newBadge) {
                        newBadge.remove();
                    }
                });
                updateNotificationCount();
            }
        });
    });

    // Supprimer une notification
    document.querySelectorAll('.delete-notification-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                const notificationId = this.dataset.id;
                const notificationItem = this.closest('.notification-item');
                
                fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        notificationItem.remove();
                        updateNotificationCount();
                        
                        // Si plus de notifications, afficher le message vide
                        if (document.querySelectorAll('.notification-item').length === 0) {
                            location.reload();
                        }
                    }
                });
            }
        });
    });

    // Mettre à jour le compteur de notifications
    function updateNotificationCount() {
        fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('#notification-badge');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        });
    }
});
</script>
@endpush
@endsection 