<?php

class ProductModel extends CI_Model
{
    function CheckToken($token)
    {
        // $token = $this->input->get_request_header('Authorization');
        $q = $this->db->get_where('sellertoken', array('token' => $token))->result();
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

    function CheckPorduct($pcode)
    {
        $q = $this->db->get_where('product', array('pcode' => $pcode))->result();

        if (empty($q)) {
            return false;
        } else {
            return true;
        }
    }

    function AddProduct($data)
    {

        $this->db->insert('product', $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        } else {
            return false;
        }
    }

    function FindProduct($n)
    {
        $this->db->select('*');
        $this->db->from('product');
        $this->db->where('pname', $n);

        return $this->db->get()->result_array();
    }
    function FindSid($n)
    {
        $this->db->select('*');
        $this->db->from('product');
        $this->db->where('pid', $n);

        $q = $this->db->get()->row();
        // return $this->db->get()->result_array();
        $q = (array)$q;
        return $q;
        // print_r($q);
        // die;
    }
    function UpdateProduct($id, $data)
    {
        return $this->db->where('pid', $id)->update('product', $data);
    }

    public function DeleteProduct($id)
    {
        return $this->db->delete('product', ['pid' => $id]);
    }
}
