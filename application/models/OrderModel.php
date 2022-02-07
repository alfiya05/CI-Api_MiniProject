<?php

class OrderModel extends CI_Model
{
    function CheckToken($token)
    {
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
    }

    //loading the products
    function GetProducts()
    {
        $this->db->select('*');
        $this->db->from('product');
        $q = $this->db->get()->result_array();
        $q = json_encode($q);
        return $q;
    }

    function AddUserAddress($data)
    {
        $this->db->insert('useraddress', $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        } else {
            return false;
        }
    }



    function PlaceOrder($data)
    {
        $this->db->select('*');
        $this->db->from('product');
        $this->db->where('pid', $data['pid']);
        $q = $this->db->get()->row_array();
        $this->db->insert('orders', array('pid' => $data['pid'], 'uid' => $data['uid'], 'oprice' => $q['pprice'], 'quantity' => $data['quantity'], 'ototal' => $q['pprice'] * $data['quantity']));
        if ($this->db->affected_rows() == '1') {

            $this->db->where('pid', $data['pid'])->update('product', array('pstock' => $q['pstock'] - $data['quantity']));

            $r = $this->db->get_where('orders', array('quantity' => $data['quantity'], 'pid' => $data['pid'], 'uid' => $data['uid'], 'oprice' => $q['pprice']))->row_array();
            // print_r($r);
            $this->db->insert('shipment', array('oid' => $r['oid']));


            return true;
        } else {
            return false;
        }
    }
    function ViewOrder($oid)
    {
        $this->db->select('t1.oid,t2.pid,t2.pname,t2.pcode,t1.quantity,t1.ototal,t4.firstname as name,t3.address,t3.postalcode,t3.city,t3.state,t3.country,t1.ordertime');
        $this->db->from('orders as t1');
        $this->db->where('t1.oid', $oid);
        $this->db->join('product as t2', 't1.pid = t2.pid');
        $this->db->join('useraddress as t3', 't1.uid = t3.uid');
        $this->db->join('user as t4', 't1.uid = t4.uid');

        $this->db->order_by('t1.ordertime', 'desc');
        return $this->db->get()->row_array();
    }

    function ViewUserOrder($uid)
    {
        $this->db->select('t1.oid,t1.uid, t2.pid,t2.pname,t2.pcode,t1.quantity,t1.ototal,t1.ordertime');
        $this->db->from('orders as t1');
        $this->db->where('t1.uid', $uid);
        $this->db->join('product as t2', 't1.pid = t2.pid');

        $this->db->order_by('t1.ordertime', 'desc');
        return $this->db->get()->result_array();
    }

    function CancelOrder($oid)
    {
        $q = $this->db->get_where('orders', array('oid' => $oid))->row_array();
        $p = $this->db->get_where('product', array('pid' => $q['pid']))->row_array();

        $this->db->delete('orders', ['oid' => $oid]);
        if ($this->db->affected_rows() == '1') {
            $this->db->where('pid', $q['pid'])->update('product', array('pstock' => $p['pstock'] + $q['quantity']));
            return true;
        } else {
            return false;
        }
    }
}
