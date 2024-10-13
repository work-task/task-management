<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\ResponseFormatter;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    private const AUTH_HEADER_NAME = 'X-Api-Key';

    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function supports(Request $request): bool
    {
        return true;
    }

    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get(self::AUTH_HEADER_NAME);
        if (null === $apiKey) {
            throw new CustomUserMessageAuthenticationException('Missing "X-Api-Key" header');
        }

        $user = $this->userRepository->findOneBy(['apiKey' => $apiKey]);
        if (null === $user) {
            throw new CustomUserMessageAuthenticationException('Invalid API key');
        }

        return new SelfValidatingPassport(new UserBadge($user->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return ResponseFormatter::errors([$message]);
    }
}
