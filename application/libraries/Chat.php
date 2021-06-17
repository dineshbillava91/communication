<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
        $this->userresources = [];
        $this->messages = [];
        $this->messageInsert = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $CI =& get_instance();
        $CI->load->model('ChatModel');
        
        $this->users[$conn->resourceId] = $conn;

        $uriQuery = $conn->httpRequest->getUri()->getQuery();
        parse_str($uriQuery, $parameters);
        $email = $parameters['userId'];

        if (isset($email)) {
            if (isset($this->userresources[$email])) {
                if (!in_array($conn->resourceId, $this->userresources[$email])) {
                    $this->userresources[$email][] = $conn->resourceId;
                }
            } else {
                $this->userresources[$email] = [];
                $this->userresources[$email][] = $conn->resourceId;
            }
        }

        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $userDetails = $CI->ChatModel->getUserInfo($email);
        $CI->ChatModel->deleteUserConnection($email);

        $userData['cn_user'] = $email;
        $userData['cn_resource_id'] = $conn->resourceId;
        $userData['cn_user_name'] = $userDetails['us_first_name']." ".$userDetails['us_last_name'];

        $CI->ChatModel->saveConnection($userData);

        foreach ($this->userresources as $email => $userData) {
            $roomConnection = $CI->ChatModel->getUserRooms($email);
            $users = $CI->ChatModel->getUserConnection($email);

            $connection['users'] = array_merge($roomConnection, $users);
            foreach ($userData as $key => $resourceId) {
                $this->users[$resourceId]->send(json_encode($connection));
            }
        }

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $conn, $msg)
    {
        $CI =& get_instance();
        $CI->load->model('ChatModel');

        $data = json_decode($msg);
        $userDetails = $CI->ChatModel->getUserInfo($data->from);

        if (isset($data->command)) {
            switch ($data->command) {
                case "chathistory":
                    $roomUsers = $CI->ChatModel->roomUsers($data->channel);
                    $roomDatails = $CI->ChatModel->roomDetails($data->channel);
                    $toUserDetails = $CI->ChatModel->getUserInfo($data->to);

                    if ($data->to || $data->channel) {
                        $saveSession['as_user'] = $data->from;
                        $saveSession['as_current_user'] = $data->to;
                        $saveSession['as_current_room'] = $data->channel;
                    
                        $CI->ChatModel->saveSession($saveSession);
                    }

                    if ($data->channel) {
                        $message['type'] = 0;
                        $message['group'] = $roomDatails['rm_name'];
                        $message['group_id'] = $data->channel;
                        $message['chatUsers'] = $roomUsers;
                    } else {
                        $message['type'] = 1;
                        $message['group'] = $toUserDetails['us_first_name']." ".$toUserDetails['us_last_name'];
                        $message['chatUsers'][] = $data->to;
                    }

                    if ($data->to || $data->channel) {
                        $dbMessages = $CI->ChatModel->getAllMessages($data->from, $data->to, $data->channel);
                        $tempMessages = $this->getMessages($data->from, $data->to, $data->channel);
                        
                        $message['messages'] = array_merge($dbMessages, $tempMessages);
                    }

                    foreach ($this->userresources as $email => $userData) {
                        if (($data->channel && in_array($email, $roomUsers)) || !$data->channel) {
                            foreach ($userData as $key => $resourceId) {
                                if ($data->channel || (!$data->channel && ($email == $data->from || $email == $data->to))) {
                                    $this->users[$resourceId]->send(json_encode($message));
                                }
                            }
                        }
                    }

                    break;
                case "groupchat":
                    //
                    $roomUsers = $CI->ChatModel->roomUsers($data->channel);
                    $roomDatails = $CI->ChatModel->roomDetails($data->channel);
                    $message['type'] = 0;
                    $message['group'] = $roomDatails['rm_name'];
                    $message['group_id'] = $data->channel;

                    $saveSession['as_user'] = $data->from;
                    $saveSession['as_current_user'] = $data->to;
                    $saveSession['as_current_room'] = $data->channel;
                
                    $CI->ChatModel->saveSession($saveSession);

                    $dbMessages = $CI->ChatModel->getAllMessages($data->from, $data->to, $data->channel);
                    $tempMessages = $this->getMessages($data->from, $data->to, $data->channel);
                    
                    $message['messages'] = array_merge($dbMessages, $tempMessages);
                    
                    $this->messages[] = $message['messages'][] = [
                        'group' => $data->channel,
                        'message' => $data->message,
                        'email' => $data->from,
                        'author' => $userDetails['us_first_name']." ".$userDetails['us_last_name'],
                        'image' => base_url().'assets/images/user.png',
                        'time' => date('Y-m-d H:i:s')
                    ];

                    $saveMessage['ms_from'] = $data->from;
                    $saveMessage['ms_message'] = $data->message;
                    $saveMessage['ms_private'] = 1;
                    $saveMessage['ms_room'] = $data->channel;
                    $saveMessage['ms_created_on'] = date('Y-m-d H:i:s');

                    $this->messageInsert[] = $saveMessage;

                    // $CI->ChatModel->saveMessage($saveMessage);

                    if (!empty($this->userresources)) {
                        foreach ($this->userresources as $email => $userData) {
                            if (in_array($email, $roomUsers)) {
                                $message['chatUsers'][] = $email;
                                foreach ($userData as $key => $resourceId) {
                                    if ($resourceId != $conn->resourceId) {
                                        $this->users[$resourceId]->send(json_encode($message));
                                    }
                                }
                            }
                        }
                    }
                    break;
                case "message":
                    //
                    $UserDetails = $CI->ChatModel->getUserInfo($data->from);
                    $message['type'] = 1;
                    $message['group'] = $UserDetails['us_first_name']." ".$UserDetails['us_last_name'];
                    $message['chatUsers'][] = $data->from;

                    $saveSession['as_user'] = $data->from;
                    $saveSession['as_current_user'] = $data->to;
                    $saveSession['as_current_room'] = $data->channel;
                
                    $CI->ChatModel->saveSession($saveSession);
                    
                    $dbMessages = $CI->ChatModel->getAllMessages($data->from, $data->to, '');
                    $tempMessages = $this->getMessages($data->from, $data->to, '');

                    $message['messages'] = array_merge($dbMessages, $tempMessages);
                    
                    $this->messages[] = $message['messages'][] = [
                        'group' => '',
                        'message' => $data->message,
                        'email' => $data->from,
                        'author' => $userDetails['us_first_name']." ".$userDetails['us_last_name'],
                        'image' => base_url().'assets/images/user.png',
                        'time' => date('Y-m-d H:i:s')
                    ];

                    $saveMessage['ms_from'] = $data->from;
                    $saveMessage['ms_message'] = $data->message;
                    $saveMessage['ms_to'] = $data->to;
                    $saveMessage['ms_private'] = 1;
                    $saveMessage['ms_created_on'] = date('Y-m-d H:i:s');

                    $this->messageInsert[] = $saveMessage;

                    // $CI->ChatModel->saveMessage($saveMessage);

                    if (isset($this->userresources[$data->to])) {
                        foreach ($this->userresources[$data->to] as $key => $resourceId) {
                            if (isset($this->users[$resourceId])) {
                                $this->users[$resourceId]->send(json_encode($message));
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $CI =& get_instance();
        $CI->load->model('ChatModel');

        $CI->ChatModel->bulkSaveMessage($this->messageInsert);

        unset($this->messages);
        unset($this->messageInsert);

        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";

        unset($this->users[$conn->resourceId]);

        foreach ($this->userresources as $email => &$userId) {
            foreach ($userId as $key => $resourceId) {
                if ($resourceId==$conn->resourceId) {
                    $CI->ChatModel->deleteUserConnection($email);
                    unset($userId[$key]);
                }
            }
        }
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function getMessages($from, $to, $group)
    {
        $messages = array();
        if (!empty($this->messages)) {
            foreach ($this->messages as $message) {
                if ((!$group && ($message['email'] == $from || $message['email'] == $to)) || $message['group'] == $group) {
                    $messages[] = $message;
                }
            }
        }

        return $messages;
    }
}
