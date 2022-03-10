<?php

declare(strict_types=1);

namespace App\Application\Websocket\Controller;

use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\Annotation\SocketIONamespace;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\Socket;
use Hyperf\SocketIOServer\SocketIO;
use Hyperf\Utils\ApplicationContext;

/**
 * @SocketIONamespace("/")
 */
class WebsocketController extends BaseNamespace
{
    /**
     * @Event("client_list")
     */
    function clientList(Socket $socket, $data)
    {
        $io = ApplicationContext::getContainer()
            ->get(SocketIO::class);

        $adapter = $io->of('/chat')
            ->getAdapter();
        $clients = $adapter->clients();
        $client_list = [];
        foreach ($clients as $client) {
            $client_list[] = [
                'client' => $client,
                'room_list' => $adapter->clientRooms($client),
            ];
        }
        $s_id = $socket->getSid();
        $socket->emit('client_list', compact('client_list', 's_id'));
    }
}
