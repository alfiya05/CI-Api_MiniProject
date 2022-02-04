<?php

class OrderModel extends CI_Model
{
    function CheckToken($token)
    {
        // $token = $this->input->get_request_header('Authorization');
        $q = $this->db->get_where('usertoken', array('token' => $token))->result();
        if (empty($q)) {
            return false;
        } else {
            return true;
        }
    }

    function DecodeToken()
    {
        $jwt = new JWT;
        $JwtSecretKey = "MyLoginKey";
        $token = $this->input->get_request_header('Authorization');
        $re = $jwt->decode($token, $JwtSecretKey, "HS256");
        $re = (array)$re;
        return $re;
        // print_r($re);
        // die;
    }

    //loading the products
    function GetProducts()
    {
        $this->db->select('*');
        $this->db->from('product');
        $q = $this->db->get()->result_array();
        // $q = (array)$q;
        // print_r($q);
        $q = json_encode($q);
        // $q = array_merge(...$q);
        // print_r(json_decode($q));
        // die;
        return $q;
    }
}
