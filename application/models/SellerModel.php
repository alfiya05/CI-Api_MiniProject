<?php

class SellerModel extends CI_Model
{

    function CheckSellerEmail($email)
    {
        $q = $this->db->get_where('seller', array('email' => $email))->result();

        if (empty($q)) {
            return false;
        } else {
            return true;
        }
    }

    function AddSeller($data)
    {

        $this->db->insert('seller', $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        } else {
            return false;
        }
    }

    function FindSeller($email)
    {
        $this->db->select('*');
        $this->db->from('seller');
        $this->db->where('email', $email);

        return $this->db->get()->row_array();
    }

    function SaveToken($data)
    {
        if (empty($this->db->get_where('sellertoken', array('sid' => $data['sid']))->result())) {

            $this->db->insert('sellertoken', $data);
        } else {
            $this->db->where(array('sid' => $data['sid']));
            $this->db->update('sellertoken', $data);
        }
    }
}
