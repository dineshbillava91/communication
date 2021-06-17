<?php
/**
* Chat is for initializing the chat application
*
* Chat is a class that his used to load chat
* display rooms, create room, edit room, delete room
*
* @package Chat
* @author Dinesh
* @version 1.0
* @access public
*/
class Chat extends CI_Controller
{
    public function __construct()
    {
        parent:: __construct();
        $this->load->model('ChatModel');
    }

    /**
    * By default this function will be called
    *
    * @param string no parameters required
    * @return array it will not return any value, just load the chat window
    * @access public
    */
    public function index()
    {
        $data['activeSesssion'] = $this->ChatModel->getActiveSession($this->session->userdata('email'));
        $this->load->view('chat_room', $data);
    }

    /**
    * Display the rooms created
    *
    * @param string no parameters required
    * @return array it will not return any value, just load the room created
    * @access public
    */
    public function rooms()
    {
        $data['rooms'] = $this->ChatModel->getRooms();
        $data['users'] = $this->ChatModel->getUserDetails();
        $this->load->view('rooms', $data);
    }

    /**
    * Function used to create rooms
    *
    * @param string no parameters required
    * @return array it will not return any value, just load the room creation window
    * @access public
    */
    public function createRoom()
    {
        $data['users'] = $this->ChatModel->getUserDetails();
        $this->load->view('create_room', $data);
    }

    /**
    * Function used to save the room details
    *
    * @param string @room_name the room name
    * @param string @users the user added to room
    * @return array after storing the data redirected to rooms landing page
    * @access public
    */
    public function saveRoom()
    {
        $room_id = $_POST['room_id'];
        $data['rm_name'] = $_POST['room_name'];
        $data['rm_users'] = json_encode($_POST['users']);
        $data['rm_created_by'] = $this->session->userdata('email');

        $this->ChatModel->saveRoom($data, $room_id);

        redirect('chat');
    }

    /**
    * Function used to edit the room details
    *
    * @param string @room_id the room id
    * @return array it will not return any value, just load the room edit page
    * @access public
    */
    public function editRoom($room_id)
    {
        $data['room'] = $this->ChatModel->editRoom($room_id);
        $data['users'] = $this->ChatModel->getUserDetails();

        if (!empty($data['room'])) {
            $this->load->view('create_room', $data);
        } else {
            echo "Unauthorized Access";
        }
    }

    /**
    * Function used to delete the room details
    *
    * @param string @room_id the room id
    * @return array it will not return any value, just delete the room
    * @access public
    */
    public function deleteRoom($room_id)
    {
        $data['room'] = $this->ChatModel->editRoom($room_id);

        if (!empty($data['room'])) {
            $this->ChatModel->deleteRoom($room_id);
            redirect('chat/rooms');
        } else {
            echo "Unauthorized Access";
        }
    }

    public function chatRoom($room_id)
    {
        $room_id = urldecode(base64_decode($room_id));
        $data['room_id'] = $room_id;
        $data['activeSession'] = $this->ChatModel->getActiveSession($this->session->userdata('email'));
        $data['activeSession']['as_current_room'] = $room_id;
        $this->load->view('chat_room', $data);
    }
}
