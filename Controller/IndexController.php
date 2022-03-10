<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/4 16:01
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Websocket\Controller;

use App\Annotation\View;
use App\Application\Admin\Controller\AdminAbstractController;
use App\Application\Admin\Lib\RenderParam;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/websocket/index")
 */
class IndexController extends AdminAbstractController
{
    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {

        $port = $this->request->getUri()
            ->getPort();
        $host = $this->request->getUri()
            ->getHost();

        $domain = 'ws://' . $host . ($port === 80 ? '' : ':' . $port);

        return RenderParam::display('index', [
            'user_token' => $this->request->cookie('HCMS_SESSION_ID'), //将session作为user_token
            'domain' => $domain
        ]);
    }
}