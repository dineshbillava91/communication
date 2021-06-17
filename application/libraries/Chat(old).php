<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $uriQuery = $conn->httpRequest->getUri()->getQuery();
        parse_str($uriQuery, $parameters);
        $email = $parameters['access_token'];
        $recipient = $parameters['recipient'];

        // Store the new connection to send messages to later
        $this->clients->attach($conn);

        $CI =& get_instance();
        $CI->load->model('ChatModel');

        $userDetails = $CI->ChatModel->getUserDetails($email);
        $CI->ChatModel->deleteUserConnection($email);

        if ($email) {
            $userData['cn_user'] = $email;
            $userData['cn_resource_id'] = $conn->resourceId;
            $userData['cn_user_name'] = $userDetails['us_first_name']." ".$userDetails['us_last_name'];
            $userData['cn_private'] = 0;
            $userData['cn_recipient_resource'] = 0;

            if ($recipient) {
                $cn_recipient_resource = $CI->ChatModel->getRecipientResource($recipient);

                $userData['cn_private'] = 1;
                $userData['cn_recipient'] = $recipient;
                $userData['cn_recipient_resource'] = $cn_recipient_resource;
            }

            $CI->ChatModel->saveConnection($userData);
        }

        $connection['users'] = $CI->ChatModel->getAllConnections();
        $connection['messages'] = $CI->ChatModel->getAllMessages($email);

        foreach ($this->clients as $client) {
            $client->send(json_encode($connection));
        }

        //echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $numRecv = count($this->clients) - 1;
        //echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n", $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $CI =& get_instance();
        $CI->load->model('ChatModel');
        
        $image = base_url().'assets/images/user.png';

        if ($CI->session->userdata('user_image')) {
            $image = base_url().'uploads/'.$CI->session->userdata('user_image');
        }

        $userInfo = $CI->ChatModel->getUserInfo($from->resourceId);
        $fromEmail = $userInfo['cn_user'];
        $fromUser = $userInfo['cn_user_name'];
        $isPrivate = $userInfo['cn_private'];
        $recipientResource = $userInfo['cn_recipient_resource'];

        $message['ms_from'] = $fromEmail;
        $message['ms_message'] = $msg;
        $message['ms_private'] = $isPrivate;

        $CI->ChatModel->saveMessage($message);
        
        foreach ($this->clients as $client) {
            if ($from !== $client) {
                if (($isPrivate == 1 && $client->resourceId == $recipientResource) || ($isPrivate == 0)) {
                    echo $client->resourceId."--".$recipientResource."<br/>";
                    $data['messages'][] = [
                        'message' => $msg,
                        'author' => $fromUser,
                        'image' => $image,
                        'time' => date('Y-m-d H:i:s'),
                        'type' => 0
                    ];
                    // The sender is not the receiver, send to each client connected
                    $client->send(json_encode($data));
                }

            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
