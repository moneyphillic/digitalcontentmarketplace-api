<?php

namespace AppBundle\Utils;

use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\Container;

class UserData
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;

    }

    public function createNewUser($data, $user) {
        $userRepoObj = $this->container->get('app.repo.userdata');
        $encoder = $this->container->get('security.password_encoder');

        $hashedPassword = $encoder->encodePassword(
            $user,
            $data['password']
        );

        $data['hashedpassword'] = $hashedPassword;

        $response = $userRepoObj->createNewUser($data, $user);

        return $response;
    }

    public function checkForMail($email) {
        $userRepoObj = $this->container->get('app.repo.userdata');

        $response = $userRepoObj->checkForMail($email);
        $message = !isset($response) ? 'Email is not used yet' : false;

        return $message;
    }

    public function getUserData($userid) {
        $userRepoObj = $this->container->get('app.repo.userdata');
        $userData = $userRepoObj->getUserData($userid);

        $response = isset($userData) ? $userData : false;

        return $response;
    }

    public function getUserDataByMail($email) {
        $userRepoObj = $this->container->get('app.repo.userdata');
        $userData = $userRepoObj->getUserDataByMail($email);

        $response = isset($userData) ? $userData : false;

        return $response;
    }

    public function checkForPassword($userid, $password) {
        $encoder = $this->container->get('security.password_encoder');
        $userRepoObj = $this->container->get('app.repo.userdata');
        $user = $userRepoObj->getUser($userid);

        $response = $encoder->isPasswordValid($user, $password);

        return $response;
    }

}