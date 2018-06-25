<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserDataRepository {

    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getUser($userid) {
        $user = $this->em->getRepository(User::class)->find($userid);

        return $user;
    }

    public function getUserData($userid) {
        $user = $this->em->createQuery(
            'SELECT u.id, u.email, u.username, u.ethAddress
                    FROM AppBundle:User u
                    WHERE u.id = :userid')
            ->setParameter('userid', $userid)
            ->getOneOrNullResult();

        return $user;
    }

    public function getUserDataByMail($email) {
        $user = $this->em->createQuery(
            'SELECT u.id, u.email, u.username, u.ethAddress, u.password
                    FROM AppBundle:User u
                    WHERE u.email = :email')
            ->setParameter('email', $email)
            ->getOneOrNullResult();

        return $user;
    }

    public function createNewUser($data, $user) {

        $user->setPassword($data['hashedpassword']);
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setEthAddress($data['ethaddress']);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function checkForMail($email) {
        $email = $this->em->createQuery(
            'SELECT u.email
                    FROM AppBundle:User u
                    WHERE u.email = :email')
            ->setParameter('email', $email)
            ->getOneOrNullResult();

        return $email;
    }

}
