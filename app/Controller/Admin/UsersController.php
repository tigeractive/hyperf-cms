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

namespace App\Controller\Admin;

use App\CodeResponse;
use App\Common\service\AdminToken;
use App\Common\service\UsersService;
use App\Common\validate\UsersValidate;
use App\Controller\AbstractController;
use App\helpers\Common;
use App\Middleware\JwtMiddleware;
use App\ReturnData;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

#[Controller]
class UsersController extends AbstractController
{
    #[Inject]
    protected ResponseInterface $response;

    #[Inject]
    private UsersService $usersService;

    #[Inject]
    private ValidatorFactoryInterface $validatorFactory;

    // 用户登录
    public function login()
    {
        (new UsersValidate())->goCheck($this->validatorFactory, 'login');
        $data = $this->request->all();
        // 判断用户是否存在
        $user = $this->usersService->getUserByName($data['username']);
        if (empty($user)) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::LOGINERROR);
        }
        // 判断密码是否正确
        if ($user->password != Common::packagePassword($data['password'])) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::LOGINERROR);
        }

        $this->usersService->updateLoginInfo($user->user_id, $this->request->getServerParams()['remote_addr'] ?? '');

        // 组装token,这个不是字符串，要转化字符串则$token->toString()
        $token = AdminToken::getInstance()->generateToken('user_id', $user->user_id, '+12 hour');

        $data = [
            'username' => $user->username,
            'token' => $token->toString(),
        ];
        return ReturnData::getInstance()->show($this->response, CodeResponse::SUCCESS, $data);
    }

    #[GetMapping(path: 'list')]
    #[Middleware(JwtMiddleware::class)]
    public function list(RequestInterface $request)
    {
        $data = $request->all();
        $result = $this->usersService->getUserList($request, $data);
        return ReturnData::getInstance()->show($this->response, CodeResponse::SUCCESS, $result);
    }

    #[GetMapping(path: 'permission')]
    #[Middleware(JwtMiddleware::class)]
    public function getPermissionList()
    {
        $data = [
            'menuList' => Context::get('menuList'),
            'actionList' => Context::get('actionList'),
        ];
        return ReturnData::getInstance()->show($this->response, CodeResponse::SUCCESS, $data);
    }

    #[PostMapping(path: 'operate')]
    #[Middleware(JwtMiddleware::class)]
    public function opreate(RequestInterface $request)
    {
        $data = $request->all();
        $data = Common::trimArr($data);
        if (! empty($data['action'])) {
            if ($data['action'] === CodeResponse::ADD) {
                return $this->add($data);
            }
            if ($data['action'] === CodeResponse::EDIT) {
                return $this->edit($data);
            }
        }
    }

    protected function add($data)
    {
        (new UsersValidate())->goCheck($this->validatorFactory, 'add');
        $data['password'] = Common::packagePassword($data['password']);
        $result = UsersService::getInstance()->add($data);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::USERADDSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::USERADDFAIL);
    }

    protected function edit($data)
    {
        (new UsersValidate())->goCheck($this->validatorFactory, 'edit');
        $result = UsersService::getInstance()->edit($data);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::USEREDITSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::USEREDITFAIL);
    }

    // 单个/批量删除 硬删除
    #[PostMapping(path: 'del')]
    public function delUsers()
    {
        (new UsersValidate())->goCheck($this->validatorFactory, 'del');
        $data = $this->request->all();
        if ($data['user_id'] == 1) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::USERDELFAIL);
        }
        $result = $this->usersService->del($data['user_id']);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::USERDELSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::USERDELFAIL);
    }

}
