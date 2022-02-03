<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/RestController.php';

use  chriskacerguis\RestServer\RestController;


class User extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function index_get()
    {
        // echo "I am RESTful API";
    }

    public function userSignUp_post()
    {

        $user = new UserModel;
        $data = array(
            'firstname' => $this->post('firstname'),
            'lastname' => $this->post('lastname'),
            'email' => $this->post('email'),
            'password' => $this->post('password')
        );
        if (!empty($data['firstname']) && !empty($data['lastname']) && !empty($data['email']) && !empty($data['password'])) {
            if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {


                $res1 = $user->CheckUserEmail($data['email']);

                if ($res1 == false) {

                    $q = $user->AddUser($data);
                    if ($q == true) {
                        $jwt = new JWT;
                        $JwtSecretKey = "MyLoginKey";

                        $getid = $user->FindUser($data['email']);

                        $token_det = array(
                            'uid' => $getid['uid'],
                            'email' => $data['email'],

                        );
                        $token = $jwt->encode($token_det, $JwtSecretKey, 'HS256');

                        $arr = array(
                            'uid' => $getid['uid'],
                            'token' => $token
                        );
                        $user->SaveToken($arr);
                        $this->response($data['firstname'] . " registered Successfully!! with token : " . $token, RestController::HTTP_OK);
                    } else {

                        $this->response("User not registered", RestController::HTTP_BAD_REQUEST);
                    }
                } else {

                    $this->response("Email already taken", RestController::HTTP_BAD_REQUEST);
                }
            } else {

                $this->response("Enter a valid email address", RestController::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response("Enter all the required fields", RestController::HTTP_BAD_REQUEST);
        }
    }


    public function userLogin_post()
    {
        $user = new UserModel;
        $email = $this->post('email');
        $password = $this->post('password');
        if (!empty($email) && !empty($password)) {

            $res = $user->FindUser($email);

            if ($res > 0) {
                if ($res['password'] == $password) {

                    $jwt = new JWT;
                    $JwtSecretKey = "MyLoginKey";

                    $token_det = array(
                        'uid' => $res['uid'],
                        'email' => $res['email'],

                    );
                    $token = $jwt->encode($token_det, $JwtSecretKey, 'HS256');

                    $arr = array(
                        'uid' => $res['uid'],
                        'token' => $token
                    );
                    $user->SaveToken($arr);
                    $this->response($res['firstname'] . " has Logged In Successfully! with token: " . $token, RestController::HTTP_OK);
                } else {

                    $this->response("Wrong Password", RestController::HTTP_BAD_REQUEST);
                }
            } else {

                $this->response("Invalid Username", RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response("Enter both Username and Password", RestController::HTTP_BAD_REQUEST);
        }
    }
}
