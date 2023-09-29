<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class JWTAuthenticationSuccessListener
{
    public function __construct()
    {
    }

    public function onJwtAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        $data = [
            "status" => true,
            ...$event->getData(),
            "user" => [
                "email" => $user->getEmail(),
                "pseudo" => $user->getPseudo()
            ],
            "message" => "Vous êtes maintenant connecté."
        ];

        if ($user->getStatus() === 0) {
            $data = [
                "status" => false,
                "message" => "Vous n'avez pas confirmé votre email."
            ];
        }

        if ($user->getStatus() === 2) {
            $data = [
                "status" => false,
                "message" => "Votre compte est inactif."
            ];
        }

        $event->setData($data);
    }


}