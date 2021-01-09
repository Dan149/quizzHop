<?php
namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AbstractSocialAuthenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

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
