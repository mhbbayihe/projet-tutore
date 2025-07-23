<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Post; // Assurez-vous d'importer le modèle Post
use App\Models\User; // Assurez-vous d'importer le modèle User
use App\Models\Comment; // Assurez-vous d'importer le modèle Comment
use App\Models\Report; // Assurez-vous d'importer le modèle Report
use Illuminate\Notifications\DatabaseNotification; // Pour le type hint

class NotificationsDropdown extends Component
{
    public $notifications; // Collection des notifications
    public $unreadCount; // Compteur des notifications non lues
    public $isOpen = false; // État d'ouverture/fermeture du dropdown

    // Ancienne syntaxe des listeners, à noter pour une éventuelle modernisation vers #[On]
    protected $listeners = [];

    /**
     * Initialise le composant au montage.
     * Charge les notifications et configure l'écouteur pour les notifications en temps réel.
     */
    public function mount()
    {
        $this->loadNotifications();

        // Configure l'écouteur Broadcast pour les notifications créées pour l'utilisateur
        if (Auth::check()) {
            $userId = Auth::id();
            // L'événement est : App\Models\User.{ID_UTILISATEUR} sur le canal privé.
            // Le nom de l'événement est : .Illuminate\Notifications\Events\BroadcastNotificationCreated
            $this->listeners["echo:App.Models.User.{$userId},\\.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated"] = 'loadNotifications';
        }
    }

    /**
     * Charge les notifications de l'utilisateur authentifié.
     * Récupère les 10 dernières notifications et le nombre de notifications non lues.
     */
    public function loadNotifications()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->notifications = $user->notifications()->latest()->take(10)->get();
            $this->unreadCount = $user->unreadNotifications()->count();

            // Chargement dynamique du contenu lié pour chaque notification
            foreach ($this->notifications as $notification) {
                $notification->relatedContent = null; // Initialise la propriété

                // Si la notification est liée à un Post
                if (isset($notification->data['post_id'])) {
                    $notification->relatedContent = Post::find($notification->data['post_id']);
                }
                // Si la notification est liée à un Utilisateur (ex: demande d'ami, mention)
                else if (isset($notification->data['user_id'])) {
                    $notification->relatedContent = User::find($notification->data['user_id']);
                }
                // Si la notification est liée à un Commentaire
                else if (isset($notification->data['comment_id'])) {
                    $notification->relatedContent = Comment::find($notification->data['comment_id']);
                    // Si le commentaire a un post, on peut aussi l'attacher pour le lien
                    if ($notification->relatedContent && $notification->relatedContent->post) {
                        $notification->relatedContent->post_id = $notification->relatedContent->post->id;
                    }
                }
                // Si la notification est liée à un Rapport et que le reportable est un Post
                else if (isset($notification->data['report_id'])) {
                    $report = Report::find($notification->data['report_id']);
                    if ($report && $report->reportable_type === Post::class) {
                        $notification->relatedContent = $report->reportable;
                    }
                }
                // Ajoutez ici d'autres types de contenu lié selon vos besoins (ex: Communauté, etc.)
            }
        } else {
            $this->notifications = collect();
            $this->unreadCount = 0;
        }
    }

    /**
     * Marque une notification spécifique comme lue.
     *
     * @param string $notificationId L'ID de la notification à marquer comme lue.
     */
    public function markAsRead($notificationId)
    {
        if (Auth::check()) {
            $notification = Auth::user()->notifications->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->loadNotifications(); // Recharge les notifications pour mettre à jour la vue
            }
        }
    }

    /**
     * Marque toutes les notifications non lues de l'utilisateur comme lues.
     */
    public function markAllAsRead()
    {
        if (Auth::check()) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->loadNotifications(); // Recharge les notifications pour mettre à jour la vue
        }
    }

    /**
     * Bascule l'état d'ouverture/fermeture du dropdown.
     * Si le dropdown s'ouvre, toutes les notifications sont marquées comme lues.
     */
    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->markAllAsRead(); // Marque toutes les notifications comme lues à l'ouverture
        }
    }

    /**
     * Affiche la vue du composant.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render()
    {
        return view('livewire.notifications-dropdown'); // Assurez-vous que le nom de la vue correspond
    }
}
