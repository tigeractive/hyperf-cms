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
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class RolesController extends AbstractController
{
    #[Inject]
    private RolesService $rolesService;

    #[Inject]
    private ValidatorFactoryInterface $validatorFactory;

    // 角色列表
    public function list()
    {
        $data = $this->request->all();
        $list = $this->rolesService->getRoleList($this->request, $data);
        return ReturnData::getInstance()->show($this->response, CodeResponse::SUCCESS, $list);
    }

    // 获取所有角色
    public function allRoles()
    {
        $list = $this->rolesService->getAllRoles()->toArray();
        return ReturnData::getInstance()->show($this->response, CodeResponse::SUCCESS, $list);
    }

    // 角色添加、编辑
    public function operate()
    {
        $data = $this->request->all();
        $data = Common::trimArr($data);
        if ($data['action'] === CodeResponse::ADD) {
            return $this->add($data);
        }
        if ($data['action'] === CodeResponse::EDIT) {
            return $this->edit($data);
        }
    }

    // 删除
    public function del()
    {
        (new RolesValidate())->goCheck($this->validatorFactory, 'del');
        $data = $this->request->all();
        if ($data['role_id'] == 1) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESDELETEFAIL);
        }
        $result = $this->rolesService->del($data['role_id']);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::USERDELSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESDELETEFAIL);
    }

    // 更新权限
    public function updatePermission()
    {
        $data = $this->request->all();
        $result = $this->rolesService->updatePermission($data);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESPERMISSIONSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESPERMISSIONFAIL);
    }

    protected function add($data)
    {
        (new RolesValidate())->goCheck($this->validatorFactory, 'add');
        $result = $this->rolesService->add($data);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESADDSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESADDFAIL);
    }

    protected function edit($data)
    {
        (new RolesValidate())->goCheck($this->validatorFactory, 'edit');
        $data = $this->request->post();
        $result = $this->rolesService->edit($data);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESEDITSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::ROLESEDITFAIL);
    }
}
