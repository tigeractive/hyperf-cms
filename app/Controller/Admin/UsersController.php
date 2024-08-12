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
    protected ValidatorFactoryInterface $validatorFactory;

    public function __construct(ValidatorFactoryInterface $validatorFactory)
    {
        $this->validatorFactory = $validatorFactory;
    }

    // 用户登录
    public function login(RequestInterface $request, ResponseInterface $response)
    {
        (new UsersValidate())->goCheck($this->validatorFactory, $request, 'login');
        $data = $request->all();
        // 判断用户是否存在
        $user = (new UsersService())->getUserByName($data['username']);
        if (empty($user)) {
            return ReturnData::getInstance()->show($response, CodeResponse::LOGINERROR);
        }
        // 判断密码是否正确
        if (! password_verify($data['password'], $user->password)) {
            return ReturnData::getInstance()->show($response, CodeResponse::LOGINERROR);
        }

        (new UsersService())->updateLoginInfo($user->user_id, $request->getServerParams()['remote_addr'] ?? '');

        // 组装token,这个不是字符串，要转化字符串则$token->toString()
        $token = AdminToken::getInstance()->generateToken('user_id', $user->user_id, '+12 hour');

        $data = [
            'username' => $user->username,
            'token' => $token->toString(),
        ];
        return ReturnData::getInstance()->show($response, CodeResponse::SUCCESS, $data);
    }

    #[GetMapping(path: 'list')]
    #[Middleware(JwtMiddleware::class)]
    public function list(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->all();
        $result = (new UsersService())->getUserList($request, $data);
        return ReturnData::getInstance()->show($response, CodeResponse::SUCCESS, $result);
    }

    #[GetMapping(path: 'permission')]
    #[Middleware(JwtMiddleware::class)]
    public function getPermissionList(ResponseInterface $response)
    {
        $data = [
            'menuList' => Context::get('menuList'),
            'actionList' => Context::get('actionList'),
        ];
        return ReturnData::getInstance()->show($response, CodeResponse::SUCCESS, $data);
    }

    #[PostMapping(path: 'operate')]
    #[Middleware(JwtMiddleware::class)]
    public function opreate(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->all();
        $data = Common::trimArr($data);
        if (! empty($data['action'])) {
            if ($data['action'] === CodeResponse::ADD) {
                return $this->add($data, $request, $response);
            }
            if ($data['action'] === CodeResponse::EDIT) {
                return $this->edit($data, $request, $response);
            }
        }
    }

    // 单个/批量删除 硬删除
    #[PostMapping(path: 'del')]
    public function delUsers(RequestInterface $request, ResponseInterface $response)
    {
        (new UsersValidate())->goCheck($this->validatorFactory, $request, 'del');
        $data = $request->all();
        if ($data['user_id'] == 1) {
            return ReturnData::getInstance()->show($response, CodeResponse::USERDELFAIL);
        }
        $result = (new UsersService())->del($data['user_id']);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::USERDELSUCCESS);
        }
        return ReturnData::getInstance()->show($response, CodeResponse::USERDELFAIL);
    }

    protected function add($data, $request, $response)
    {
        (new UsersValidate())->goCheck($this->validatorFactory, $request, 'add');
        $data['password'] = Common::packagePassword($data['password']);
        $result = UsersService::getInstance()->add($data);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::USERADDSUCCESS);
        }
        return ReturnData::getInstance()->show($response, CodeResponse::USERADDFAIL);
    }

    protected function edit($data, $request, $response)
    {
        (new UsersValidate())->goCheck($this->validatorFactory, $request, 'edit');
        $result = UsersService::getInstance()->edit($data);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::USEREDITSUCCESS);
        }
        return ReturnData::getInstance()->show($response, CodeResponse::USEREDITFAIL);
    }
}
