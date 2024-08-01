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
use App\Common\service\RolesService;
use App\Common\validate\RolesValidate;
use App\Controller\AbstractController;
use App\helpers\Common;
use App\ReturnData;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class RolesController extends AbstractController
{
    protected RolesService $rolesService;

    protected ValidatorFactoryInterface $validatorFactory;

    public function __construct(RolesService $rolesService, ValidatorFactoryInterface $validatorFactory)
    {
        $this->rolesService = $rolesService;
        $this->validatorFactory = $validatorFactory;
    }

    // 角色列表
    public function list(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->all();
        $data = Common::trimArr($data);
        $list = $this->rolesService->getRoleList($request, $data);
        return ReturnData::getInstance()->show($response, CodeResponse::SUCCESS, $list);
    }

    // 获取所有角色
    public function allRoles(ResponseInterface $response)
    {
        $list = $this->rolesService->getAllRoles()->toArray();
        return ReturnData::getInstance()->show($response, CodeResponse::SUCCESS, $list);
    }

    // 角色添加、编辑
    public function operate(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->all();
        $data = Common::trimArr($data);
        if ($data['action'] === CodeResponse::ADD) {
            return $this->add($data, $request, $response);
        }
        if ($data['action'] === CodeResponse::EDIT) {
            return $this->edit($data, $request, $response);
        }
    }

    // 删除
    public function del(RequestInterface $request, ResponseInterface $response)
    {
        (new RolesValidate())->goCheck($this->validatorFactory, $request, 'del');
        $data = $request->all();
        if ($data['role_id'] == 1) {
            return ReturnData::getInstance()->show($response, CodeResponse::ROLESDELETEFAIL);
        }
        $result = $this->rolesService->del($data['role_id']);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::USERDELSUCCESS);
        }
        return ReturnData::getInstance()->show($response, CodeResponse::ROLESDELETEFAIL);
    }

    // 更新权限
    public function updatePermission(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->all();
        $result = $this->rolesService->updatePermission($data);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::ROLESPERMISSIONSUCCESS);
        }
        return ReturnData::getInstance()->show($response, CodeResponse::ROLESPERMISSIONFAIL);
    }

    protected function add($data, $request, $response)
    {
        (new RolesValidate())->goCheck($this->validatorFactory, $request, 'add');
        $result = $this->rolesService->add($data);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::ROLESADDSUCCESS);
        }
        return ReturnData::getInstance()->show($response, CodeResponse::ROLESADDFAIL);
    }

    protected function edit($data, $request, $response)
    {
        (new RolesValidate())->goCheck($this->validatorFactory, $request, 'edit');
        $data = $request->post();
        $result = $this->rolesService->edit($data);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::ROLESEDITSUCCESS);
        }
        return ReturnData::getInstance()->show($response, CodeResponse::ROLESEDITFAIL);
    }
}
