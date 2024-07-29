<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        // 设置CORS响应头
        $response = $response->withHeader('Access-Control-Allow-Origin', '*'); // 或者具体的域名
        $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Access-Control-Request-Method, Access-Control-Request-Headers');
        $response = $response->withHeader('Access-Control-Expose-Headers', '*');
        $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

        // 对于预检请求（OPTIONS），可以直接返回响应，不继续执行后续逻辑
        if ($request->getMethod() === 'OPTIONS') {
            return $response;
        }

        return $response;
    }
}
