<?php

declare(strict_types=1);

namespace App\Security;

use jirik1h\ZaslatAuthBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

final class ApiAuthenticator extends AbstractAuthenticator
{
    private const API_HEADER = 'x-api-key';

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::API_HEADER);
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get(self::API_HEADER);
        if ($token === null) {
            throw new CustomUserMessageAuthenticationException('No API token provided.');
        }

        $user = $this->userRepository->findByToken($token);
        if ($user === null) {
            throw new CustomUserMessageAuthenticationException('API token is not valid.');
        }

        return new SelfValidatingPassport(new UserBadge($user->getEmail() ?? '', static function () use ($user): UserInterface {
            return $user;
        }));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => $exception->getMessage(),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
