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
use App\Common\service\MenusService;
use App\Common\service\RolesService;
use App\Common\validate\MenusValidate;
use App\Controller\AbstractController;
use App\Exception\ParameterException;
use App\helpers\Common;
use App\ReturnData;
use Exception;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class MenusController extends AbstractController
{
    #[Inject]
    protected MenusService $menusService;

    #[Inject]
    protected ValidatorFactoryInterface $validatorFactory;

    // 获取菜单列表
    public function list()
    {
        $data = $this->request->all();
        $list = $this->menusService->getMenuList($data);
        return ReturnData::getInstance()->show($this->response, CodeResponse::SUCCESS, $list);
    }

    public function operate()
    {
        $data = $this->request->all();
        if (! empty($data['action'])) {
            if ($data['action'] === CodeResponse::ADD) {
                return $this->add($data);
            }
            if ($data['action'] === CodeResponse::EDIT) {
                return $this->edit($data);
            }
        }
    }

    // 删除
    public function del()
    {
        (new MenusValidate())->goCheck($this->validatorFactory, 'del');
        $data = $this->request->all();
        Db::beginTransaction();
        try {
            $result = MenusService::getInstance()->del($data['menu_id']);
            $roleResult = RolesService::getInstance()->updateMenuIdList($data['menu_id']);
            // 如果没有异常，则提交事务
            Db::commit();
            if ($result && $roleResult) {
                return ReturnData::getInstance()->show($this->response, CodeResponse::MENUSDELSUCCESS);
            }
            return ReturnData::getInstance()->show($this->response, CodeResponse::MENUSDELFAIL);
        } catch (Exception $e) {
            // 如果捕获到异常，则回滚事务
            Db::rollBack();
        }
    }

    protected function add($data)
    {
        $data = Common::trimArr($data);
        (new MenusValidate())->goCheck($this->validatorFactory, 'add');
        $result = $this->menusService->add($data);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::MENUSADDSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::MENUSADDFAIL);
    }

    protected function edit($data)
    {
        $data = Common::trimArr($data);
        (new MenusValidate())->goCheck($this->validatorFactory, 'edit');
        $result = $this->menusService->edit($data);
        if ($result) {
            return ReturnData::getInstance()->show($this->response, CodeResponse::MENUSEDITSUCCESS);
        }
        return ReturnData::getInstance()->show($this->response, CodeResponse::MENUSEDITFAIL);
    }
}
