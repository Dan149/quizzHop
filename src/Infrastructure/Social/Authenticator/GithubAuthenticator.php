<?php
namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AbstractSocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class GithubAuthenticator extends AbstractSocialAuthenticator
{
    protected string $serviceName = 'github';

    public function getUserFromResourceOwner(ResourceOwnerInterface $githubUser, UserRepository $repository): ?User
    {
        if (!($githubUser instanceof GithubResourceOwner)) {
            throw new \RuntimeException("Expecting GithubResourceOwner as the first parameter");
        }

        $user = $repository->findOrCreateFromOauth('github', $githubUser->getId(), $githubUser->getNickname());

        if ($user && null === $user->getGithubId()) {
            $user->setGithubId($githubUser->getId());
            $this->em->flush();
        }

        return $user;
    }
}
