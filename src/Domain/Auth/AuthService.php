<?php
namespace App\Security;

use App\Domain\Auth\User;
use Symfony\Component\HttpFoundation\Request;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Service to simplify the communication with the authentication and offer a stricter type.
 */
class AuthService
{
    private TokenStorageInterface $tokenStorage;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getUser(): User
    {
        $user = $this->getUserOrNull();
        if (null === $user) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    public function getUserOrNull(): ?User
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();
        if (!\is_object($user)) {
            return null;
        }

        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }

    public function logout(?Request $request = null): void
    {
        $request = $request ?: new Request();
        $this->eventDispatcher->dispatch(new LogoutEvent($request, $this->tokenStorage->getToken()));
        $this->tokenStorage->setToken(null);
    }
}
