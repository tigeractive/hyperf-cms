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
use App\Controller\Admin\MenusController;
use App\Controller\Admin\RolesController;
use App\Controller\Admin\UsersController;
use App\Middleware\JwtMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::get('/favicon.ico', function () {
    return '';
});

Router::addGroup('/admin', function () {
    Router::post('/users/login', [UsersController::class, 'login']);

    // 角色
    Router::get('/roles/list', [RolesController::class, 'list'], ['middleware' => [JwtMiddleware::class]]);
    Router::get('/roles/all', [RolesController::class, 'allRoles'], ['middleware' => [JwtMiddleware::class]]);
    Router::post('/roles/operate', [RolesController::class, 'operate'], ['middleware' => [JwtMiddleware::class]]);
    Router::post('/roles/del', [RolesController::class, 'del'], ['middleware' => [JwtMiddleware::class]]);
    Router::post('/roles/update-permission', [RolesController::class, 'updatePermission'], ['middleware' => [JwtMiddleware::class]]);

    // 菜单
    Router::get('/menus/list', [MenusController::class, 'list'], ['middleware' => [JwtMiddleware::class]]);
    Router::get('/menus/parent-list', [MenusController::class, 'parentList'], ['middleware' => [JwtMiddleware::class]]);
    Router::post('/menus/operate', [MenusController::class, 'operate'], ['middleware' => [JwtMiddleware::class]]);
    Router::post('/menus/del', [MenusController::class, 'del'], ['middleware' => [JwtMiddleware::class]]);
});

Router::addRoute(['OPTIONS'], '/{path:.+}', function () {
    return '';
});
