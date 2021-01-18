<?php
namespace App\Infrastructure\Social\Authenticator;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use App\Infrastructure\Social\Authenticator\AbstractSocialAuthenticator;

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
