<?php
namespace App\Infrastructure\Social\Authenticator;

use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Domain\Auth\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Bundle\SecurityBundle\Security\UserAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

abstract class AbstractSocialAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    protected string $serviceName = '';
    private ClientRegistry $clientRegistry;
    protected EntityManagerInterface $em;
    private RouterInterface $router;
    private AuthService $authService;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        RouterInterface $router,
        AuthService $authService
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->authService = $authService;
    }

    public function supports(Request $request)
    {
        if ('' === $this->serviceName) {
            throw new \Exception("You must set a \$serviceName property");
        }

        return 'oauth_check' === $request->attributes->get('_route') && $request->get('service') === $this->serviceName;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('login'));
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getClient());
    }

    /**
     * @param AccessToken $credentials
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        $resourceOwner = $this->getResourceOwnerFromCredentials($credentials);
        $user = $this->authService->getUserOrNull();
        if ($user) {
            throw new \Exception('User already connected');
        }

        /** @var UserRepository $repository */
        $repository = $this->em->getRepository(User::class);
        $user = $this->getUserFromResourceOwner($resourceOwner, $repository);
        if (null == $user) {
            throw new \Exception('UserOauth not found');
        }

        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);
        return new RedirectResponse($targetPath ?: '/');
    }

    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    protected function getUserFromResourceOwner(
        ResourceOwnerInterface $resourceOwner,
        UserRepository $userRepository
    ): ?User {
        return null;
    }

    private function getClient(): OAuth2Client
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }
}
