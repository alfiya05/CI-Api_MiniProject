<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/Format.php';
require APPPATH . 'libraries/RestController.php';

use  chriskacerguis\RestServer\RestController;


class Products extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProductModel');

        $token = $this->input->get_request_header('Authorization');
        if (!$token) {
            $output = array("Error" => "Access Denied");
            $this->response($output, RestController::HTTP_UNAUTHORIZED);
        } else {
            $q = $this->ProductModel->CheckToken($token);
            if ($q == false) {
                $output = array("Error" => "Access Denied to this ");
                $this->response($output, RestController::HTTP_UNAUTHORIZED);
            }
        }
    }

    public function index_get()
    {
        echo "I am RESTful API";
    }



    public function storeProduct_post()
    {
        $prod = new ProductModel;
        // print_r($re);
        // die;
        $re = $prod->DecodeToken();
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
                $this->response("Product with this code already Registered!", RestController::HTTP_BAD_REQUEST);
            }
        } else {
            $this->response("Enter all the required fields", RestController::HTTP_BAD_REQUEST);
        }
    }

    public function getProduct_get($pname)
    {
        $p = new ProductModel;
        $p1 = $p->FindProduct($pname);
        $this->response($p1, 200);
    }

    public function updateProduct_put($id)
    {
        $prod = new ProductModel;
        $data = [
            'pname' =>  $this->put('pname'),
            'pprice' => $this->put('pprice'),
            'pstock' => $this->put('pstock')
        ];
        $re = $prod->DecodeToken();
        $pid = $prod->FindSid($id);
        if (!empty($pid)) {
            if ($re['sid'] == $pid['sid']) {
                $result = $prod->UpdateProduct($id, $data);
                if ($result > 0) {
                    $this->response('Product details UPDATED', RestController::HTTP_OK);
                } else {
                    $this->response('Failed to update details', RestController::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response(['Error' => 'Unauthorised seller'], RestController::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response('This product does not exist', RestController::HTTP_BAD_REQUEST);
        }
    }

    public function deleteProduct_delete($id)
    {
        $prod = new ProductModel;
        $re = $prod->DecodeToken();
        $pid = $prod->FindSid($id);
        if (!empty($pid)) {
            if ($re['sid'] == $pid['sid']) {
                $result = $prod->DeleteProduct($id);
                if ($result > 0) {
                    $this->response('Product DELETED', RestController::HTTP_OK);
                } else {
                    $this->response('FAILED TO DELETE product', RestController::HTTP_BAD_REQUEST);
                }
            } else {
                $this->response(['Error' => 'Unauthorised seller'], RestController::HTTP_UNAUTHORIZED);
            }
        } else {
            $this->response('This product does not exist', RestController::HTTP_BAD_REQUEST);
        }
    }
}
