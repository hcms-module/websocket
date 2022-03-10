<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/10 11:50
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Websocket\Kernel;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\SocketIOServer\Parser\Decoder;
use Hyperf\SocketIOServer\Parser\Encoder;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\SocketIOServer\SocketIO;
use Hyperf\SocketIOServer\SocketIOConfig;
use Hyperf\WebSocketServer\Sender;
use Psr\Container\ContainerInterface;

/**
 * 主要用设置ping\pong 的过期时间
 * 需要在 dependencies.php 配置  Hyperf\SocketIOServer\SocketIO::class => \App\Application\Websocket\Kernel\SocketIOFactory::class
 */
class SocketIOFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $io = new SocketIO($container->get(StdoutLoggerInterface::class), $container->get(Sender::class),
            $container->get(Decoder::class), $container->get(Encoder::class),
            $container->get(SidProviderInterface::class));

        // 重写 pingTimeout 参数
        $config = $container->get(SocketIOConfig::class);
        $config->setPingTimeout(3000);
        $config->setClientCallbackTimeout(3000);

        return $io;
    }
}