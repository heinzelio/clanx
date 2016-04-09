<?php
namespace AppBundle\Entity;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    // implement the UserLoaderInterface
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.mail = :username AND u.verified = :verified')
            ->setParameter('username', $username)
            ->setParameter('verified', 1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
?>
