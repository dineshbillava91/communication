<?php
/**
* ChatModel is for chat related db operation
*
* ChatModel is a class that his used to load connection
* save messages, display messages, user details
* create room, delete room
*
* @package ChatModel
* @author Dinesh
* @version 1.0
* @access public
*/
class ChatModel extends CI_Model
{
    public function __construct()
    {
        parent:: __construct();
    }

    /**
    * Used to get all registerd user details
    *
    * @param string no parameters required
    * @return array it will return registered user list
    * @access public
    */
    public function getUserDetails()
    {
        $qry = $this->db->query("SELECT * FROM users WHERE us_status=1");
    
        $result = array();
        if ($qry->num_rows() > 0) {
            foreach ($qry->result_array() as $row) {
                $result[$row['us_id']] = $row;
            }
        }

        return $result;
    }

    /**
    * Used to delete connection
    *
    * @param string @email user email
    * @return array it will not return any value
    * @access public
    */
    public function deleteUserConnection($email)
    {
        $qry = $this->db->query("DELETE FROM connections WHERE cn_user='$email'");
    }

    /**
    * Used to get room associated for a particular user
    *
    * @param string @email user email
    * @return array it will return user room details
    * @access public
    */
    public function getUserRooms($email)
    {
        $result = array();

        $userDetails = $this->getUserInfo($email);
        $userId = $userDetails['us_id'];

        $qry = $this->db->query("SELECT rm_id,rm_name FROM rooms WHERE rm_users like '%\"$userId\"%' AND rm_status=1");
        if ($qry->num_rows() > 0) {
            foreach ($qry->result_array() as $row) {
                $tmp_array = array();

                $tmp_array['cn_user_name'] = $row['rm_name'];
                $tmp_array['cn_user'] = '';
                $tmp_array['us_image'] = '';
                $tmp_array['rm_id'] = $row['rm_id'];
                $tmp_array['email'] = $email;


                $result[] = $tmp_array;
            }
        }

        return $result;
    }

    /**
    * Used to get user coonection details
    *
    * @param string @email user email
    * @return array it will return user connection details
    * @access public
    */
    public function getUserConnection($email)
    {
        $qry = $this->db->query("SELECT cn_user_name,cn_user,us_image FROM connections,users WHERE cn_user=us_email AND us_email!='$email'");

        if ($qry->num_rows() > 0) {
            return $qry->result_array();
        } else {
            return array();
        }
    }

    /**
    * Used to save coonection details
    *
    * @param string @data connection details
    * @return array it will return connection id
    * @access public
    */
    public function saveConnection($data)
    {
        $this->db->insert('connections', $data);
        return $this->db->insert_id();
    }

    /**
    * Used to get user details
    *
    * @param string @email user emailid
    * @return array it will return user details
    * @access public
    */
    public function getUserInfo($email)
    {
        $qry = $this->db->query("SELECT * FROM users WHERE us_email='$email' AND us_status=1");
        return $qry->row_array();
    }

    /**
    * Used to save message details
    *
    * @param string @data message details
    * @return array it will return message id
    * @access public
    */
    public function saveMessage($data)
    {
        $this->db->insert('messages', $data);
        return $this->db->insert_id();
    }

    /**
    * Used to save bulk message
    *
    * @param string @data bulk messages
    * @return array it will return last message id
    * @access public
    */
    public function bulkSaveMessage($data)
    {
        $this->db->insert_batch('messages', $data);
        return $this->db->insert_id();
    }

    /**
    * Used to get all messages
    *
    * @param string @fromEmail from user emailid
    * @param string @toEmail to user emailid
    * @param string @room room id
    * @return array it will return message details
    * @access public
    */
    public function getAllMessages($fromEmail, $toEmail, $room)
    {
        $toCond = "";

        if ($toEmail) {
            $toCond = " ms_from in ('".$fromEmail."','".$toEmail."') AND ms_to in ('".$fromEmail."','".$toEmail."')";
        }

        if ($room) {
            $toCond = " ms_room=$room";
        }

        $qry = $this->db->query("SELECT ms_from,ms_message,us_first_name,us_last_name,us_image,ms_created_on FROM messages LEFT JOIN users ON ms_from=us_email WHERE $toCond");
        $messages = array();

        if ($qry->num_rows() > 0) {
            foreach ($qry->result_array() as $row) {
                $message['message'] = $row['ms_message'];
                $message['email'] = $row['ms_from'];
                $message['author'] = $row['us_first_name']." ".$row['us_last_name'];
                if ($row['us_image']) {
                    $message['image'] = base_url().'uploads/'.$row['us_image'];
                } else {
                    $message['image'] = base_url().'assets/images/user.png';
                }
                $message['time'] = $row['ms_created_on'];

                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
    * Used to get all user details
    *
    * @param string no parameters required
    * @return array it will return all user details
    * @access public
    */
    public function getAllUserDetails()
    {
        $qry = $this->db->query("SELECT us_first_name,us_last_name,us_email,us_image FROM users WHERE us_status=1");
        
        $result = array();

        if ($qry->num_rows() > 0) {
            foreach ($qry->result_array() as $row) {
                $tmp_array = array();
                $tmp_array['name'] = $row['us_first_name']." ".$row['us_last_name'];
                $tmp_array['image'] = $row['image'];
                $result[$row['us_email']] = $tmp_array;
            }
        }

        return $result;
    }

    /**
    * Used to get all rooms
    *
    * @param string no parameters required
    * @return array it will return all rooms
    * @access public
    */
    public function getRooms()
    {
        $email = $this->session->userdata('email');
        $qry = $this->db->query("SELECT * FROM rooms WHERE rm_created_by='$email' AND rm_status=1");
        return $qry->result_array();
    }

    /**
    * Used to save room details
    *
    * @param string no parameters required
    * @return array it will return all rooms
    * @access public
    */
    public function saveRoom($data, $room_id)
    {
        $room = $data['rm_name'];
        $email = $this->session->userdata('email');

        $qry = $this->db->query("SELECT * FROM rooms WHERE rm_name='$room' AND rm_created_by='$email' AND rm_id!=$room_id");
        
        if ($qry->num_rows() > 0) {
            $message = "Room already exists with the same name";
            $this->session->set_userdata('error', $message);
        } else {
            if ($room_id) {
                $updateData['rm_name'] = $data['rm_name'];
                $updateData['rm_users'] = $data['rm_users'];
                $updateData['rm_modified_on'] = date('Y-m-d H:i:s');

                $this->db->where('rm_id', $room_id);
                $this->db->update('rooms', $updateData);
                $message = "Room Updated Successfully";
                $this->session->set_userdata('success', $message);
            } else {
                $this->db->insert('rooms', $data);
                $message = "Room Created Successfully";
                $this->session->set_userdata('success', $message);
                $room_id = $this->db->insert_id();
            }

            $saveSession['as_user'] = $email;
            $saveSession['as_current_user'] = '';
            $saveSession['as_current_room'] = $room_id;

            $this->saveSession($saveSession);
        }
    }

    /**
    * Used to get room details
    *
    * @param string @room_id room id
    * @return array it will return room details
    * @access public
    */
    public function roomDetails($room_id)
    {
        $qry = $this->db->query("SELECT * FROM rooms WHERE rm_id='$room_id' AND rm_status=1");

        if ($qry->num_rows() > 0) {
            return $qry->row_array();
        } else {
            return array();
        }
    }

    /**
    * Used to edit room details
    *
    * @param string @room_id room id
    * @return array it will return room details
    * @access public
    */
    public function editRoom($room_id)
    {
        $email = $this->session->userdata('email');

        $qry = $this->db->query("SELECT * FROM rooms WHERE rm_id='$room_id' AND rm_created_by='$email' AND rm_status=1");

        if ($qry->num_rows() > 0) {
            return $qry->row_array();
        } else {
            return array();
        }
    }

    /**
    * Used to delete room details
    *
    * @param string @room_id room id
    * @return array it will not return any value
    * @access public
    */
    public function deleteRoom($room_id)
    {
        $email = $this->session->userdata('email');

        $deleteData['rm_status'] = 0;
        $deleteData['rm_modified_on'] = date('Y-m-d H:i:s');

        $this->db->where('rm_id', $room_id);
        $this->db->update('rooms', $deleteData);

        $message = "Room Deleted Successfully";
        $this->session->set_userdata('error', $message);
    }

    /**
    * Used to get room user details
    *
    * @param string @room_id room id
    * @return array it will return room users
    * @access public
    */
    public function roomUsers($room_id)
    {
        $user_list = $this->getUserDetails();

        $qry = $this->db->query("SELECT rm_users FROM rooms WHERE rm_id='$room_id' AND rm_status=1");

        $room_users = array();
        if ($qry->num_rows() > 0) {
            $users = json_decode($qry->row()->rm_users, true);

            foreach ($users as $user) {
                $room_users[] = $user_list[$user]['us_email'];
            }
        }

        return $room_users;
    }

    /**
    * Used to save user session details
    *
    * @param string @data user active session details
    * @return array it will not return any value
    * @access public
    */
    public function saveSession($data)
    {
        $user = $data['as_user'];

        $qry = $this->db->query("SELECT as_user FROM active_sessions WHERE as_user='$user'");
        if ($qry->num_rows() > 0) {
            unset($data[$user]);
            $this->db->where('as_user', $user);
            $this->db->update('active_sessions', $data);
        } else {
            $this->db->insert('active_sessions', $data);
        }
    }

    /**
    * Used to get user session details
    *
    * @param string @email user emailid
    * @return array it will return user active session
    * @access public
    */
    public function getActiveSession($email)
    {
        $qry = $this->db->query("SELECT * FROM active_sessions WHERE as_user='$email'");

        return $qry->row_array();
    }
}
