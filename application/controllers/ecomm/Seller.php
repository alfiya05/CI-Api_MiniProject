<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/RestController.php';

use  chriskacerguis\RestServer\RestController;


class Seller extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('SellerModel');
    }


    public function index_get()
    {
        echo "I am RESTful API";
    }

    public function sellerSignUp_post()
    {

        $user = new SellerModel;
        $data = array(
            'firstname' => $this->post('firstname'),
            'lastname' => $this->post('lastname'),
            'email' => $this->post('email'),
            'password' => $this->post('password')
        );
        if (!empty($data['firstname']) && !empty($data['lastname']) && !empty($data['email']) && !empty($data['password'])) {
            if (filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {


                $res1 = $user->CheckSellerEmail($data['email']);

                if ($res1 == false) {

                    $q = $user->AddSeller($data);
                    if ($q == true) {
                        $jwt = new JWT;
                        $JwtSecretKey = "MyLoginKey";

                        $getid = $user->FindSeller($data['email']);

                        $token_det = array(
                            'sid' => $getid['sid'],
                            'email' => $data['email'],

                        );
                        $token = $jwt->encode($token_det, $JwtSecretKey, 'HS256');

                        $arr = array(
                            'sid' => $getid['sid'],
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


    public function sellerLogin_post()
    {
        $user = new SellerModel;
        $email = $this->post('email');
        $password = $this->post('password');
        if (!empty($email) && !empty($password)) {

            $res = $user->FindSeller($email);

            if ($res > 0) {
                if ($res['password'] == $password) {

                    $jwt = new JWT;
                    $JwtSecretKey = "MyLoginKey";

                    $token_det = array(
                        'sid' => $res['sid'],
                        'email' => $res['email'],

                    );
                    $token = $jwt->encode($token_det, $JwtSecretKey, 'HS256');

                    $arr = array(
                        'sid' => $res['sid'],
                        'token' => $token
                    );

                    $re = $jwt->decode($token, $JwtSecretKey, 'HS256');
                    print_r($re);
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
