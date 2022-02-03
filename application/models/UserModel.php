<?php

class UserModel extends CI_Model
{
    function CheckUserEmail($email)
    {
        $q = $this->db->get_where('user', array('email' => $email))->result();

        if (empty($q)) {
            return false;
        } else {
            return true;
        }
    }

    function AddUser($data)
    {

        $this->db->insert('user', $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        } else {
            return false;
        }
    }

    function FindUser($email)
    {
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('email', $email);

        return $this->db->get()->row_array();
    }

    function SaveToken($data)
    {
        if (empty($this->db->get_where('usertoken', array('uid' => $data['uid']))->result())) {

            $this->db->insert('usertoken', $data);
        } else {
            $this->db->where(array('uid' => $data['uid']));
            $this->db->update('usertoken', $data);
        }
    }
}
