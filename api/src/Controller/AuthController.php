<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name: 'Auth')]
#[Route('/api/auth')]
class AuthController extends AbstractController
{

    /**
     * @throws TransportExceptionInterface
     */
    #[OA\Post(
        path: '/api/auth/register',
        description: 'Register a new user',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: [
                    'email',
                    'pseudo',
                    'password',
                    'confirmPassword'
                ],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        example: 'user@example.com'
                    ),
                    new OA\Property(
                        property: 'pseudo',
                        type: 'string',
                        example: 'User'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        example: '123456dD+'
                    ),
                    new OA\Property(
                        property: 'confirmPassword',
                        type: 'string',
                        example: '123456dD+'
                    )
                ],
                type: 'object'
            )
        )
    )]
    #[Route('/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        MailerInterface $mailer
    ): JsonResponse
    {
        $confirmPassword = $request->toArray()["confirmPassword"];
        $content = $serializer->serialize(
            array_filter(
                $request->toArray(),
                function ($key) {
                    return $key !== "confirmPassword";
                },
                ARRAY_FILTER_USE_KEY
            ),
            'json'
        );

        /** @var User $user */
        $user = $serializer->deserialize(
            $content,
            User::class,
            'json'
        );

        $errors = $validator->validate($user);

        if (count($errors) > 0 || $user->getPassword() !== $confirmPassword) {
            $errorsArray = [];

            foreach ($errors as $error) {
                $errorsArray[$error->getPropertyPath()] = $error->getMessage();
            }

            if ($user->getPassword() !== $confirmPassword) {
                $errorsArray["password"] = "Les mots de passe ne sont pas identiques";
            }

            return new JsonResponse(
                $errorsArray,
                Response::HTTP_BAD_REQUEST
            );
        }

        $token = uniqid('tk_');
        $hash = $csrfTokenManager->getToken($token)->getValue();

        $user
            ->setCreatedAt(new \DateTimeImmutable('now'))
            ->setStatus(0)
            ->setPassword($passwordHasher->hashPassword($user, $user->getPassword()))
            ->setCEToken($hash)
            ->setCEExpiratedAt(new \DateTimeImmutable('+ 5 minutes'))
        ;

        $em->persist($user);
        $em->flush();

        $url = "https://localhost:3000/update-to-link-when-created/".$user->getPseudo()."_".$token;

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Bienvenue sur BEST ToDo App - Veuillez confirmer votre adresse email')
            ->htmlTemplate('emails/auth/register.html.twig')
            ->context([
                'pseudo' => $user->getPseudo(),
                'url' => $url
            ]);

        $mailer->send($email);

        $context = SerializationContext::create()
            ->setGroups('user:register');

        $jsonUser = $serializer->serialize([
            "status" => true,
            "data" => $user,
        ], 'json', $context);

        return new JsonResponse(
            $jsonUser,
            Response::HTTP_CREATED,
            json: true
        );
    }


    /**
     * @throws TransportExceptionInterface
     */
    #[Route(
        '/confirmation-email/{pseudo}_{token}',
        name: 'api_auth_confirmation_email',
        requirements: ['pseudo' => '[a-zA-Z0-9]+', 'token' => '(tk_){1}[a-z0-9]+'],
        methods: ['GET'])
    ]
    public function confirmationEmail(
        ?string $pseudo,
        ?string $token,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        MailerInterface $mailer
    ): JsonResponse
    {
        $user = null;

        if ($pseudo) {
            $user = $em->getRepository(User::class)->findOneBy(['pseudo' => $pseudo]);
        }

        if (!$user || !$token) {
            return new JsonResponse(
                [
                    "status" => false,
                    "message" => "La page n'a pas été trouvée."
                ],
                Response::HTTP_NOT_FOUND
            );
        }

        if (
            new \DateTimeImmutable('now') > $user->getCEExpiratedAt() ||
            !$csrfTokenManager->isTokenValid(new CsrfToken($token, $user->getCEToken()))
        ) {
            return new JsonResponse(
                [
                    "status" => false,
                    "message" => "Le token n'est plus valide.",
                    "data" => [
                        "expired" => new \DateTimeImmutable('now') > $user->getCEExpiratedAt(),
                        "valid" => $csrfTokenManager->isTokenValid(new CsrfToken($token, $user->getCEToken()))
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user
            ->setStatus(true)
            ->setCEExpiratedAt(null)
            ->setCEToken(null)
        ;

        $em->flush();

        $email = (new TemplatedEmail())
            ->to($user->getEmail())
            ->subject('Bienvenue sur BEST ToDo App - Adresse email confirmé !')
            ->htmlTemplate('emails/auth/confirm-email.html.twig')
            ->context([
                'pseudo' => $user->getPseudo()
            ]);

        $mailer->send($email);

        return new JsonResponse(
            [],
            Response::HTTP_NO_CONTENT
        );
    }
}
