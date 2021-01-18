<?php
namespace App\Infrastructure\Social\Authenticator;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use App\Infrastructure\Social\Authenticator\AbstractSocialAuthenticator;

class GoogleAuthenticator extends AbstractSocialAuthenticator
{
    protected string $serviceName = 'google';

    public function getUserFromResourceOwner(ResourceOwnerInterface $googleUser, UserRepository $repository): ?User
    {
        if (!($googleUser instanceof GoogleUser)) {
            throw new \RuntimeException("Expecting GoogleUser as the first parameter");
        }

        $user = $repository->findOrCreateFromOauth('google', $googleUser->getId(), $googleUser->getEmail());

        if ($user && null === $user->getGoogleId()) {
            $user->setGoogleId($googleUser->getId());
            $this->em->flush();
        }

        return $user;
    }
}
