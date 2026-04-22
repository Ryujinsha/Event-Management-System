<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Training;

class NotificationService
{
    public static function send(User $user, string $title, string $message, string $type = 'info', ?Training $training = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'training_id' => $training?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => false,
        ]);
    }

    public static function sendToRole(string $roleSlug, string $title, string $message, string $type = 'info', ?Training $training = null): void
    {
        $users = User::whereHas('role', fn($q) => $q->where('slug', $roleSlug))->get();

        foreach ($users as $user) {
            self::send($user, $title, $message, $type, $training);
        }
    }

    public static function sendToMultipleRoles(array $roleSlugs, string $title, string $message, string $type = 'info', ?Training $training = null): void
    {
        foreach ($roleSlugs as $slug) {
            self::sendToRole($slug, $title, $message, $type, $training);
        }
    }

    public static function notifyTrainingPublished(Training $training): void
    {
        self::sendToMultipleRoles(
            ['student', 'lecturer'],
            'New Training Available',
            "A new training \"{$training->title}\" has been published. Register now!",
            'training',
            $training
        );
    }

    public static function notifyRegistrationStatus(User $user, Training $training, string $status): void
    {
        $statusText = ucfirst($status);
        self::send(
            $user,
            "Registration {$statusText}",
            "Your registration for \"{$training->title}\" has been {$status}.",
            'registration',
            $training
        );
    }

    public static function notifyCertificateAvailable(User $user, Training $training): void
    {
        self::send(
            $user,
            'Certificate Available',
            "Your certificate for \"{$training->title}\" is now available for download.",
            'certificate',
            $training
        );
    }
}
