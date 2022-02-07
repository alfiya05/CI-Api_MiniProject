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
        $tmp = new OrderModel;
        print_r($tmp->GetProducts());
    }

    public function placeOrder_post()
    {
        $tmp = new OrderModel;
        $token = $tmp->DecodeToken();
        $data = [
            'pid' =>  $this->post('pid'),
            'uid' => $token['uid'],
            'quantity' => $this->post('quantity')
        ];

        if (!empty($data['pid']) && !empty($data['uid']) && !empty($data['quantity'])) {

            $result = $tmp->PlaceOrder($data);

            if ($result == true) {

                $this->response("Order placed Successfully!", RestController::HTTP_OK);
            } else {

                $this->response("Unable to place order", RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response("Enter all the required fields", RestController::HTTP_BAD_REQUEST);
        }
    }


    public function userAddress_post()
    {
        $tmp = new OrderModel;
        $token = $tmp->DecodeToken();
        $data = array(
            'uid' => $token['uid'],
            'address' => $this->post('address'),
            'postalcode' => $this->post('postalcode'),
            'city' => $this->post('city'),
            'state' => $this->post('state')
        );
        if (!empty($data['uid']) && !empty($data['address']) && !empty($data['postalcode']) && !empty($data['city']) && !empty($data['state'])) {
            if (filter_var($data['postalcode'], FILTER_SANITIZE_NUMBER_INT) && preg_match('^[1-9]{1}[0-9]{5}$^', $data['postalcode'])) {

                $q = $tmp->AddUserAddress($data);
                if ($q == true) {

                    $this->response("Address saved successfully.", RestController::HTTP_OK);
                } else {

                    $this->response("Address not saved.", RestController::HTTP_BAD_REQUEST);
                }
            } else {

                $this->response("Enter a valid postal code.", RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response("Enter all the required fields", RestController::HTTP_BAD_REQUEST);
        }
    }

    public function viewOrder_get($oid)
    {
        $tmp = new OrderModel;

        $q = $tmp->ViewOrder($oid);
        if (!empty($q)) {

            $this->response($q, RestController::HTTP_OK);
        } else {

            $this->response("No Order of this id exists", RestController::HTTP_BAD_REQUEST);
        }
    }

    public function viewUserOrder_get($uid)
    {
        $tmp = new OrderModel;
        $token = $tmp->DecodeToken();
        $uid = $token['uid'];
        $q = $tmp->ViewUserOrder($uid);
        if (!empty($q)) {

            $this->response($q, RestController::HTTP_OK);
        } else {

            $this->response("No Order of this user exists", RestController::HTTP_BAD_REQUEST);
        }
    }

    public function cancelOrder_delete($oid)
    {
        $tmp = new OrderModel;
        $q = $tmp->ViewOrder($oid);
        if (!empty($q)) {

            $que = $tmp->CancelOrder($oid);

            if ($que == true) {

                $this->response("Order Cancelled Successfully!", RestController::HTTP_OK);
            } else {

                $this->response("Order Not Cancelled", RestController::HTTP_BAD_REQUEST);
            }
        } else {

            $this->response("No Order of this id exists", RestController::HTTP_BAD_REQUEST);
        }
    }
}
