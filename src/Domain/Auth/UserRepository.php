<?php

namespace App\Domain\Auth;

use App\Domain\Auth\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findOrCreateFromOauth(string $service, ?string $serviceId, ?string $username): ?User
    {
        if (null === $serviceId || null === $username) {
            return null;
        }

        $user = $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->orWhere("u.{$service}Id = :serviceId")
            ->setMaxResults(1)
            ->setParameters([
                'username' => $username,
                'serviceId' => $serviceId
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($user) {
            return $user;
        }

        $service = ucfirst($service);
        $function = "set{$service}Id";
        $user = (new User())
            ->$function($serviceId)
            ->setUsername($username)
        ;

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }
}
