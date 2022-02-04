<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/RestController.php';

use  chriskacerguis\RestServer\RestController;


class Order extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('OrderModel');

        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            $output = array("Error" => "Access Denied");
            $this->response($output, RestController::HTTP_UNAUTHORIZED);
        } else {
            $q = $this->OrderModel->CheckToken($token);
            if ($q == false) {
                $output = array("Error" => "Access Denied to this ");
                $this->response($output, RestController::HTTP_UNAUTHORIZED);
            }
        }
    }

    public function index_get()
    {
        // echo "I am RESTful API";
        $tmp = new OrderModel;
        print_r($tmp->GetProducts());
    }

    public function placeOrder_post()
    {
        $tmp = new OrderModel;
        // print_r($tmp->GetProducts());
        $pd = $tmp->GetProducts();
        print_r($pd);
    }
}
