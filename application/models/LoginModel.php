<?php
/**
* LoginModel is for chat related db operation
*
* LoginModel is a class that his used to load connection
* save messages, display messages, user details
* create room, delete room
*
* @package LoginModel
* @author Dinesh
* @version 1.0
* @access public
*/
class LoginModel extends CI_Model
{
    public function __construct()
    {
        parent:: __construct();
    }

    /**
    * Used to register user details
    *
    * @param string @data user details
    * @return array it will return any value
    * @access public
    */
    public function register($data)
    {
        $email = $data['us_email'];

        $qry = $this->db->query("SELECT * FROM users  WHERE us_email='$email'");

        if ($qry->num_rows() > 0) {
            $message = "Already Registered";
            $this->session->set_userdata('error', $message);
        } else {
            $this->db->insert('users', $data);
            $message = "Registered Successfully";
            $this->session->set_userdata('success', $message);
        }
    }

    /**
    * Used to authenticate user
    *
    * @param string @email user emailid
    * @param string @password user password
    * @return array it will return true/false
    * @access public
    */
    public function authenticate($email, $password)
    {
        $qry = $this->db->query("SELECT * FROM users  WHERE us_email='$email'");

        if ($qry->num_rows() > 0) {
            $data = $qry->row_array();
            $stored_password = $data['us_password'];

            if (password_verify($password, $stored_password)) {
                $this->saveSession($data);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
    * Used to authenticate social login
    *
    * @param string @data user details
    * @return array it will not return any value
    * @access public
    */
    public function socialLogin($data)
    {
        $email = $data['us_email'];

        $qry = $this->db->query("SELECT * FROM users  WHERE us_email='$email'");

        if ($qry->num_rows() == 0) {
            $this->db->insert('users', $data);
            $user_id = $this->db->insert_id();
        } else {
            $update_data['us_social_login'] = 1;

            $this->db->where('us_email', $email);
            $this->db->update('users', $update_data);
            $user_id = $qry->row()->us_id;
        }

        $data['user_id'] = $user_id;
        $this->saveSession($data);
    }

    /**
    * Used to save session details
    *
    * @param string @data session data
    * @return array it will not return any value
    * @access public
    */
    public function saveSession($data)
    {
        $this->session->set_userdata('is_logged', 1);
        $this->session->set_userdata('user_id', $data['user_id']);
        $this->session->set_userdata('name', $data['us_first_name']." ".$data['us_last_name']);
        $this->session->set_userdata('email', $data['us_email']);
        $this->session->set_userdata('social_login', $data['us_social_login']);

        if ($data['us_image']) {
            $this->session->set_userdata('user_image', $file_name);
        }
    }

    /**
    * Used to update user profile details
    *
    * @param string @data user profile data
    * @return array it will not return any value
    * @access public
    */
    public function updateProfile($data)
    {
        $email = $this->session->userdata('email');

        $this->db->where('us_email', $email);
        $this->db->update('users', $data);

        $message = "Profile Updated Successfully";
        $this->session->set_userdata('success', $message);
    }

    /**
    * Used to update user profile details
    *
    * @param string @data user profile data
    * @return array it will not return any value
    * @access public
    */
    public function updatePassword()
    {
        $currentPassword = $_POST['current_password'];
        $password = $_POST['password'];

        $email = $this->session->userdata('email');

        $qry = $this->db->query("SELECT * FROM users  WHERE us_email='$email'");

        if ($qry->num_rows() > 0) {
            $data = $qry->row_array();
            $stored_password = $data['us_password'];

            if (password_verify($currentPassword, $stored_password)) {
                $data['us_password'] = $this->encryptPassword($password);
                $data['us_modified_on'] = date('Y-m-d H:i:s');

                $this->db->where('us_email', $email);
                $this->db->update('users', $data);

                $message = "Password Changed Successfully";
                $this->session->set_userdata('success', $message);
            } else {
                $message = "Current Password is Wrong";
                $this->session->set_userdata('error', $message);
            }
        }
    }

    /**
    * Used to encrypt user password
    *
    * @param string @password user password
    * @return array it will return encrypted password
    * @access public
    */
    public function encryptPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
