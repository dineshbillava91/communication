<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require FCPATH . '/vendor/autoload.php';

Class Server extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('chat');
    }

    public function index()
    {
        if (!is_cli()) {
            die('Good Try');
        }

        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            8080
        );

        $server->run();
    }
}
