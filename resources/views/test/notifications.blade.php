@extends('layouts.app')

@section('title', 'Test des Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Test du Système de Notifications</h1>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions de Test</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button onclick="createTestNotification()" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-bell mr-2"></i>
                    Créer une notification de test
                </button>
                
                <button onclick="createMultipleNotifications()" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-bells mr-2"></i>
                    Créer plusieurs notifications
                </button>
                
                <button onclick="showTestToast()" 
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-toast mr-2"></i>
                    Afficher un toast de test
                </button>
                
                <button onclick="checkNotifications()" 
                        class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sync mr-2"></i>
                    Vérifier les notifications
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Instructions</h2>
            
            <div class="space-y-4 text-gray-700">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                    <div>
                        <h3 class="font-medium">1. Test des Notifications</h3>
                        <p>Cliquez sur "Créer une notification de test" pour ajouter une notification dans le système.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <i class="fas fa-bell text-green-500 mt-1"></i>
                    <div>
                        <h3 class="font-medium">2. Badge de Notifications</h3>
                        <p>Le badge rouge sur l'icône de cloche dans la barre de navigation affiche le nombre de notifications non lues.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <i class="fas fa-mouse-pointer text-purple-500 mt-1"></i>
                    <div>
                        <h3 class="font-medium">3. Dropdown des Notifications</h3>
                        <p>Cliquez sur l'icône de cloche pour ouvrir le dropdown et voir toutes vos notifications.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <i class="fas fa-check text-yellow-500 mt-1"></i>
                    <div>
                        <h3 class="font-medium">4. Actions sur les Notifications</h3>
                        <p>Vous pouvez marquer une notification comme lue ou la supprimer directement depuis le dropdown.</p>
                    </div>
                </div>
                
                <div class="flex items-start space-x-3">
                    <i class="fas fa-clock text-red-500 mt-1"></i>
                    <div>
                        <h3 class="font-medium">5. Mise à Jour Automatique</h3>
                        <p>Le système vérifie automatiquement les nouvelles notifications toutes les 30 secondes.</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="test-results" class="mt-8"></div>
    </div>
</div>

<script>
async function createTestNotification() {
    try {
        const response = await fetch('/test/create-notification', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotificationToast(data.message, 'success');
            // Recharger les notifications
            if (window.notificationSystem) {
                window.notificationSystem.loadUnreadCount();
            }
        } else {
            showNotificationToast(data.error || 'Erreur lors de la création', 'danger');
        }
    } catch (error) {
        showNotificationToast('Erreur de connexion', 'danger');
    }
}

async function createMultipleNotifications() {
    try {
        const response = await fetch('/test/create-multiple-notifications', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotificationToast(data.message, 'success');
            // Recharger les notifications
            if (window.notificationSystem) {
                window.notificationSystem.loadUnreadCount();
            }
        } else {
            showNotificationToast(data.error || 'Erreur lors de la création', 'danger');
        }
    } catch (error) {
        showNotificationToast('Erreur de connexion', 'danger');
    }
}

function showTestToast() {
    showNotificationToast('Ceci est un toast de test !', 'info');
}

async function checkNotifications() {
    try {
        const response = await fetch('/notifications/unread-count');
        const data = await response.json();
        
        const resultsDiv = document.getElementById('test-results');
        resultsDiv.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-medium text-blue-900 mb-2">Résultats de la vérification</h3>
                <p class="text-blue-700">Nombre de notifications non lues : <strong>${data.count}</strong></p>
                <p class="text-blue-700 text-sm mt-2">Vérification effectuée à ${new Date().toLocaleTimeString()}</p>
            </div>
        `;
    } catch (error) {
        showNotificationToast('Erreur lors de la vérification', 'danger');
    }
}

// Fonction globale pour afficher des toasts (définie dans le layout)
function showNotificationToast(message, type = 'info') {
    if (window.showNotificationToast) {
        window.showNotificationToast(message, type);
    } else {
        alert(message); // Fallback si le système de toast n'est pas disponible
    }
}
</script>
@endsection 