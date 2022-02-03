<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/RestController.php';

use  chriskacerguis\RestServer\RestController;


class Products extends RestController
{
    public function __construct($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('ProductModel');
        $jwt = new JWT;
        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            $output = array("Error" => "Access Denied");
            $this->response($output, RestController::HTTP_UNAUTHORIZED);
        }

        $JwtSecretKey = "MyLoginKey";
        // $res = $jwt->decode($token, $this->config->item($s), array('HS256'));
        // $res = $jwt->decode($token, $s, 'HS256');
        $re = $jwt->decode($token, $JwtSecretKey, "HS256");
        // print_r($re);
    }

    public function index_get()
    {
        echo "I am RESTful API";
    }

    public function storeProduct_post()
    {
        $prod = new ProductModel;
        $jwt = new JWT;
        $JwtSecretKey = "MyLoginKey";
        $token = $this->input->get_request_header('Authorization');
        $re = $jwt->decode($token, $JwtSecretKey, "HS256");
        $re = (array)$re;
        // print_r($re);
        // die;
        $data = array(
            'sid' => $re['sid'],
            'pcode' => $this->post('pcode'),
            'pname' => $this->post('pname'),
            'pprice' => $this->post('pprice'),
            'pstock' => $this->post('pstock')
        );

        if (!empty($data['pname']) && !empty($data['pprice']) && !empty($data['pstock']) && !empty($data['pcode'])) {
            $res = $prod->CheckPorduct($data['pcode']);
            if ($res == false) {
                $q = $prod->AddProduct($data);
                if ($q == true) {

                    $this->response("Product Stored Successfully!", RestController::HTTP_OK);
                } else {

                    $this->response("Product not Stored!", RestController::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response("Same Product already Registered!", RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response("Enter all the required fields", RestController::HTTP_BAD_REQUEST);
        }
    }

    public function getProduct_get()
    {
    }

    public function updateProduct_put()
    {
    }

    public function deleteProduct_delete()
    {
    }
}
