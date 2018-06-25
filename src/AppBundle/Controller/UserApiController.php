<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Image;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class UserApiController extends Controller
{

    /**
     * Register API endpoint
     */
    public function registerAction(Request $request) {

        header('Access-Control-Allow-Origin: http://localhost:3002');

        $userObj = $this->get('app.userdata');

        $data = array();
        $response = array();
        $user = new User();

        $data['email'] = $request->get('email');
        $data['password'] = $request->get('password');
        $data['username'] = $request->get('username');
        $data['ethaddress'] = $request->get('ethaddress');

        $checkMail = $userObj->checkForMail($data['email']);

        if ($checkMail) {
            $userObj->createNewUser($data, $user);
            $response['status'] = 200;
        } else {
            $response['error'] = 'This email address is already used';
            $response['status'] = 400;
        }

        return new JsonResponse(array(
            'response' => $response,
        ));
    }

    /**
     * Login API endpoint
     */
    public function customLoginAction(Request $request) {
        header('Access-Control-Allow-Origin: http://localhost:3002');

        $userObj = $this->get('app.userdata');

        $email = $request->get('_username');
        $password = $request->get('_password');

        $response = array();

        $checkMail = $userObj->checkForMail($email);

        // If email is not registred yet
        if (!$checkMail) {
            $userData = $userObj->getUserDataByMail($email);

            $checkPassword = $userObj->checkForPassword($userData['id'], $password);

            if ($checkPassword) {
                $response['userid'] = $userData['id'];
                $response['status'] = 200;
            } else {
                $response['message'] = 'Password is incorrect';
                $response['status'] = 401;
            }

        } else {
            $response['message'] = 'This email address is not found';
            $response['status'] = 404;
        }

        return new JsonResponse(array(
            'response' => $response
        ));
    }

    /**
     * Return user data API endpoint
     */
    public function getUserDataAction(Request $request) {
        header('Access-Control-Allow-Origin: http://localhost:3002');

        $userid = $request->get('userid');

        $userObj = $this->get('app.userdata');
        $user = $userObj->getUserData($userid);

        return new JsonResponse(array(
            'user' => $user
        ));
    }

    /**
     * Upload file API endpoint
     */
    public function uploadImageAction(Request $request) {
        header('Access-Control-Allow-Origin: http://localhost:3002');

        $em = $this->getDoctrine()->getManager();

        $filename = 'image.jpg';
        $tmp_name = $_FILES['file']['tmp_name'];
        move_uploaded_file($tmp_name, 'upload/images/' . $filename);

        $image = new Image();
        $image->setUserid($request->get('user'));
        $image->setPath($filename);
        $em->persist($image);
        $em->flush();

        return new JsonResponse(array(
            'success' => $_FILES['file']
        ));
    }

}
