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

use App\Common\service\AdminToken;
use App\Common\service\MenusService;
use App\Common\service\RolesService;
use App\Common\service\UsersService;
use App\Exception\PrivilegeException;
use App\Exception\TokenException;
use App\Exception\TokenExpireException;
use App\helpers\Common;
use GuzzleHttp\Psr7\Stream;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JwtMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestUrl = $request->getUri()->getPath();
        if (empty($request->getHeader('authorization'))) {
            throw new TokenException();
        }
        $token = $request->getHeader('authorization');
        $token = explode(' ', $token[0])[1];
        // 判断token是否合法
        if (! AdminToken::getInstance()->isMy($token)) {
            throw new TokenException();
        }
        // 判断token是否已过期
        if (AdminToken::getInstance()->isExpired($token)) {
            throw new TokenExpireException();
        }

        $userId = AdminToken::getInstance()->getClaim($token, 'user_id');

        $user = UsersService::getInstance()->getUserByUserId($userId);
        if (empty($user)) {
            throw new TokenException();
        }

        // 看该用户是否用该权限，进行拦截
        $roles = RolesService::getInstance()->getRoleListByRoleIds($user->role_list);
        if (! empty($roles)) {
            $menuListId = [];
            foreach ($roles->toArray() as $k => $v) {
                foreach ($v['menu_id_list'] as $k2 => $v2) {
                    if (! empty($v2)) {
                        $menuListId[] = intval($v2);
                    }
                }
            }
            $menuListId = array_unique($menuListId);
            // 获取该用户权限菜单
            $menus = MenusService::getInstance()->getMenuListByMenuIds($menuListId);
            if (empty($menus)) {
                throw new PrivilegeException();
            }
            $menus = $menus->toArray();
            $getPriUrl = '/admin/users/permission';
            $priArr = [$getPriUrl];
            $actionList = [];
            foreach ($menus as $k => $v) {
                if (! empty($v['url'])) {
                    $priArr[] = $v['url'];
                }
                if ($v['menu_type'] == 2) {
                    $actionList[] = $v['menu_code'];
                }
            }
            if (! in_array($requestUrl, $priArr)) {
                throw new PrivilegeException();
            }
            $menuList = Common::unlimitedForLayer($menus, 'children', 'menu_id');

            if ($getPriUrl === $requestUrl) {
                Context::set('actionList', $actionList);
                Context::set('menuList', $menuList);
            }
        }

        $response = $handler->handle($request);
        $content = $response->getBody()->getContents();
        $content = json_decode($content, true);
        // 将返回字段从蛇形转为驼峰
        if (! empty($content['data'])) {
            $content['data'] = Common::snakeToCamelKeys($content['data']);

            // 注意：修改响应体需要重置响应体
            $response = $response->withBody(new Stream(fopen('php://temp', 'r+')));
            $response->getBody()->write(json_encode($content, JSON_UNESCAPED_UNICODE));
        }

        return $response;
    }
}
