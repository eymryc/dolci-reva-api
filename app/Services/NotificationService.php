<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notifier la création d'une réservation
     */
    public function notifyBookingCreated(Booking $booking)
    {
        $this->notifyCustomer($booking, 'booking_created');
        $this->notifyOwner($booking, 'booking_created');
        $this->logNotification($booking, 'booking_created');
    }
    
    /**
     * Notifier la confirmation d'une réservation
     */
    public function notifyBookingConfirmed(Booking $booking)
    {
        $this->notifyCustomer($booking, 'booking_confirmed');
        $this->logNotification($booking, 'booking_confirmed');
    }
    
    /**
     * Notifier l'annulation d'une réservation
     */
    public function notifyBookingCancelled(Booking $booking, $reason = null)
    {
        $this->notifyCustomer($booking, 'booking_cancelled', $reason);
        $this->notifyOwner($booking, 'booking_cancelled', $reason);
        $this->logNotification($booking, 'booking_cancelled', $reason);
    }
    
    /**
     * Notifier le paiement d'une réservation
     */
    public function notifyPaymentReceived(Booking $booking)
    {
        $this->notifyOwner($booking, 'payment_received');
        $this->logNotification($booking, 'payment_received');
    }
    
    /**
     * Notifier le client
     */
    private function notifyCustomer(Booking $booking, $type, $data = null)
    {
        $customer = $booking->customer;
        
        // Envoyer un email
        $this->sendEmail($customer, $type, $booking, $data);
        
        // Envoyer une notification push (à implémenter)
        $this->sendPushNotification($customer, $type, $booking, $data);
    }
    
    /**
     * Notifier le propriétaire
     */
    private function notifyOwner(Booking $booking, $type, $data = null)
    {
        $owner = $booking->owner;
        
        // Envoyer un email
        $this->sendEmail($owner, $type, $booking, $data);
        
        // Envoyer une notification push (à implémenter)
        $this->sendPushNotification($owner, $type, $booking, $data);
    }
    
    /**
     * Envoyer un email
     */
    private function sendEmail(User $user, $type, Booking $booking, $data = null)
    {
        try {
            $subject = $this->getEmailSubject($type);
            $template = $this->getEmailTemplate($type);
            
            $emailData = [
                'user' => $user,
                'booking' => $booking,
                'data' => $data
            ];
            
            // Pour l'instant, on log l'email (à remplacer par un vrai envoi)
            Log::info("Email envoyé à {$user->email}", [
                'type' => $type,
                'subject' => $subject,
                'booking_id' => $booking->id
            ]);
            
        } catch (\Exception $e) {
            Log::error("Erreur envoi email", [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Envoyer une notification push
     */
    private function sendPushNotification(User $user, $type, Booking $booking, $data = null)
    {
        // À implémenter avec un service de push notifications
        Log::info("Push notification envoyée", [
            'user_id' => $user->id,
            'type' => $type,
            'booking_id' => $booking->id
        ]);
    }
    
    /**
     * Logger la notification
     */
    private function logNotification(Booking $booking, $type, $data = null)
    {
        Log::info("Notification envoyée", [
            'booking_id' => $booking->id,
            'type' => $type,
            'customer_id' => $booking->customer_id,
            'owner_id' => $booking->owner_id,
            'data' => $data
        ]);
    }
    
    /**
     * Obtenir le sujet de l'email
     */
    private function getEmailSubject($type)
    {
        $subjects = [
            'booking_created' => 'Nouvelle réservation reçue',
            'booking_confirmed' => 'Réservation confirmée',
            'booking_cancelled' => 'Réservation annulée',
            'payment_received' => 'Paiement reçu'
        ];
        
        return $subjects[$type] ?? 'Notification DOLCIREVA';
    }
    
    /**
     * Obtenir le template d'email
     */
    private function getEmailTemplate($type)
    {
        $templates = [
            'booking_created' => 'emails.booking-created',
            'booking_confirmed' => 'emails.booking-confirmed',
            'booking_cancelled' => 'emails.booking-cancelled',
            'payment_received' => 'emails.payment-received'
        ];
        
        return $templates[$type] ?? 'emails.default';
    }
    
    /**
     * Notifier les administrateurs
     */
    public function notifyAdmins($type, $data)
    {
        $admins = User::where('type', 'ADMIN')->orWhere('type', 'SUPER_ADMIN')->get();
        
        foreach ($admins as $admin) {
            $this->sendEmail($admin, $type, null, $data);
        }
    }
}
