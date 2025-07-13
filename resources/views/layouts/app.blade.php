<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'IFran Presence') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Styles personnalisés -->
    <style>
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .scroll-animate {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-out;
        }
        
        .scroll-animate.animate {
            opacity: 1;
            transform: translateY(0);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .sidebar-open {
                transform: translateX(0);
            }
            
            .sidebar-closed {
                transform: translateX(-100%);
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('partials.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Notifications Dropdown -->
    <div id="notifications-dropdown" class="fixed top-16 right-4 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50 hidden">
        <div class="p-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
                <button id="mark-all-read" class="text-sm text-blue-600 hover:text-blue-800">
                    Tout marquer comme lu
                </button>
            </div>
        </div>

        <div id="notifications-list" class="max-h-96 overflow-y-auto">
            <!-- Les notifications seront chargées ici -->
        </div>

        <div class="p-4 border-t border-gray-200">
            <a href="/notifications" class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                Voir toutes les notifications
            </a>
        </div>
    </div>

    <!-- Notifications Toast -->
    <div id="notifications-toast" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        // Système de notifications avec JavaScript vanilla
        class NotificationSystem {
            constructor() {
                this.unreadCount = 0;
                this.notifications = [];
                this.isDropdownOpen = false;
                this.init();
            }

            init() {
                this.loadUnreadCount();
                this.setupEventListeners();
                this.startPolling();
            }

            setupEventListeners() {
                // Bouton notifications
                const notificationBtn = document.getElementById('notification-btn');
                if (notificationBtn) {
                    notificationBtn.addEventListener('click', () => this.toggleDropdown());
                }

                // Fermer le dropdown en cliquant à l'extérieur
                document.addEventListener('click', (e) => {
                    const dropdown = document.getElementById('notifications-dropdown');
                    const btn = document.getElementById('notification-btn');
                    
                    if (dropdown && !dropdown.contains(e.target) && !btn.contains(e.target)) {
                        this.closeDropdown();
                    }
                });

                // Marquer tout comme lu
                const markAllReadBtn = document.getElementById('mark-all-read');
                if (markAllReadBtn) {
                    markAllReadBtn.addEventListener('click', () => this.markAllAsRead());
                }
            }

            async loadUnreadCount() {
                try {
                    const response = await fetch('/notifications/unread-count');
                    const data = await response.json();
                    this.unreadCount = data.count || 0;
                    this.updateBadge();
                } catch (error) {
                    console.error('Erreur lors du chargement du compteur:', error);
                }
            }

            async loadNotifications() {
                try {
                    const response = await fetch('/notifications');
                    const data = await response.json();
                    this.notifications = data.notifications || [];
                    this.renderNotifications();
                } catch (error) {
                    console.error('Erreur lors du chargement des notifications:', error);
                }
            }

            renderNotifications() {
                const container = document.getElementById('notifications-list');
                if (!container) return;

                if (this.notifications.length === 0) {
                    container.innerHTML = `
                        <div class="p-4 text-center text-gray-500">
                            <i class="fas fa-bell-slash text-2xl mb-2"></i>
                            <p>Aucune notification</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = this.notifications.map(notification => `
                    <div class="p-4 hover:bg-gray-50 transition-colors border-b border-gray-100 ${notification.lu ? 'opacity-75' : ''}" 
                         data-id="${notification.id}">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <i class="${this.getNotificationIcon(notification.type)} text-lg ${this.getNotificationColor(notification.type)}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900">
                                    ${notification.titre}
                                </h4>
                                <p class="text-sm text-gray-600 mt-1">
                                    ${notification.message}
                                </p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <span class="text-xs text-gray-500">
                                        <i class="far fa-clock mr-1"></i>
                                        ${this.formatDate(notification.created_at)}
                                    </span>
                                    ${notification.action_url ? `
                                        <a href="${notification.action_url}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            ${notification.action_text || 'Voir plus'}
                                            <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    ` : ''}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                ${!notification.lu ? `
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Nouveau
                                    </span>
                                ` : ''}
                                <button onclick="notificationSystem.markAsRead('${notification.id}')" 
                                        class="text-gray-400 hover:text-gray-600" title="Marquer comme lu">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="notificationSystem.deleteNotification('${notification.id}')" 
                                        class="text-gray-400 hover:text-red-600" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }

            async markAsRead(notificationId) {
                try {
                    const response = await fetch(`/notifications/${notificationId}/mark-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        const notification = this.notifications.find(n => n.id === notificationId);
                        if (notification) {
                            notification.lu = true;
                            this.renderNotifications();
                            this.loadUnreadCount();
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors du marquage comme lu:', error);
                }
            }

            async markAllAsRead() {
                try {
                    const response = await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        this.notifications.forEach(notification => {
                            notification.lu = true;
                        });
                        this.renderNotifications();
                        this.unreadCount = 0;
                        this.updateBadge();
                    }
                } catch (error) {
                    console.error('Erreur lors du marquage de toutes les notifications:', error);
                }
            }

            async deleteNotification(notificationId) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                    return;
                }
                
                try {
                    const response = await fetch(`/notifications/${notificationId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    });
                    
                    if (response.ok) {
                        const index = this.notifications.findIndex(n => n.id === notificationId);
                        if (index > -1) {
                            this.notifications.splice(index, 1);
                            this.renderNotifications();
                            this.loadUnreadCount();
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la suppression:', error);
                }
            }

            toggleDropdown() {
                if (this.isDropdownOpen) {
                    this.closeDropdown();
                } else {
                    this.openDropdown();
                }
            }

            openDropdown() {
                const dropdown = document.getElementById('notifications-dropdown');
                if (dropdown) {
                    dropdown.classList.remove('hidden');
                    this.isDropdownOpen = true;
                    this.loadNotifications();
                }
            }

            closeDropdown() {
                const dropdown = document.getElementById('notifications-dropdown');
                if (dropdown) {
                    dropdown.classList.add('hidden');
                    this.isDropdownOpen = false;
                }
            }

            updateBadge() {
                const badge = document.getElementById('notification-badge');
                if (badge) {
                    if (this.unreadCount > 0) {
                        badge.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            }

            showToast(message, type = 'info') {
                const toast = document.createElement('div');
                toast.className = `p-4 rounded-lg shadow-lg ${this.getToastClasses(type)}`;
                toast.innerHTML = `
                    <div class="flex items-center">
                        <i class="${this.getToastIcon(type)} mr-3"></i>
                        <span>${message}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;

                const container = document.getElementById('notifications-toast');
                container.appendChild(toast);

                // Auto-remove after 5 seconds
                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.remove();
                    }
                }, 5000);
            }

            getNotificationIcon(type) {
                const icons = {
                    'info': 'fas fa-info-circle',
                    'warning': 'fas fa-exclamation-triangle',
                    'danger': 'fas fa-times-circle',
                    'success': 'fas fa-check-circle',
                    'system': 'fas fa-cog',
                    'alerte': 'fas fa-exclamation',
                    'seance': 'fas fa-calendar',
                    'presence': 'fas fa-user-check'
                };
                return icons[type] || 'fas fa-bell';
            }

            getNotificationColor(type) {
                const colors = {
                    'info': 'text-blue-500',
                    'warning': 'text-yellow-500',
                    'danger': 'text-red-500',
                    'success': 'text-green-500',
                    'system': 'text-gray-500',
                    'alerte': 'text-orange-500',
                    'seance': 'text-purple-500',
                    'presence': 'text-indigo-500'
                };
                return colors[type] || 'text-gray-500';
            }

            getToastClasses(type) {
                const classes = {
                    'info': 'bg-blue-50 border border-blue-200 text-blue-800',
                    'warning': 'bg-yellow-50 border border-yellow-200 text-yellow-800',
                    'danger': 'bg-red-50 border border-red-200 text-red-800',
                    'success': 'bg-green-50 border border-green-200 text-green-800'
                };
                return classes[type] || classes['info'];
            }

            getToastIcon(type) {
                const icons = {
                    'info': 'fas fa-info-circle',
                    'warning': 'fas fa-exclamation-triangle',
                    'danger': 'fas fa-times-circle',
                    'success': 'fas fa-check-circle'
                };
                return icons[type] || icons['info'];
            }

            formatDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diffInMinutes = Math.floor((now - date) / (1000 * 60));
                
                if (diffInMinutes < 1) {
                    return 'À l\'instant';
                } else if (diffInMinutes < 60) {
                    return `Il y a ${diffInMinutes} min`;
                } else if (diffInMinutes < 1440) {
                    const hours = Math.floor(diffInMinutes / 60);
                    return `Il y a ${hours}h`;
                } else {
                    const days = Math.floor(diffInMinutes / 1440);
                    return `Il y a ${days}j`;
                }
            }

            startPolling() {
                setInterval(() => {
                    this.loadUnreadCount();
                }, 30000); // Vérifier toutes les 30 secondes
            }
        }

        // Initialiser le système de notifications
        let notificationSystem;
        document.addEventListener('DOMContentLoaded', function() {
            notificationSystem = new NotificationSystem();
        });

        // Fonction globale pour afficher des toasts
        function showNotificationToast(message, type = 'info') {
            if (notificationSystem) {
                notificationSystem.showToast(message, type);
            }
        }
    </script>

    @stack('scripts')
</body>
</html>