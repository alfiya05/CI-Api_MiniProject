<?php

class ProductModel extends CI_Model
{

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

    function FindSeller($email)
    {
        $this->db->select('*');
        $this->db->from('product');
        $this->db->where('email', $email);

        return $this->db->get()->row_array();
    }
}
