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
use App\helpers\Common;
use App\ReturnData;
use Exception;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use function Hyperf\Translation\trans;

class MenusController extends AbstractController
{
    protected MenusService $menusService;
    protected ValidatorFactoryInterface $validatorFactory;

    public function __construct(MenusService $menusService, ValidatorFactoryInterface $validatorFactory)
    {
        $this->menusService = $menusService;
        $this->validatorFactory = $validatorFactory;
    }

    // 获取菜单列表
    public function list(RequestInterface $request, ResponseInterface $response)
    {
        $data = $request->all();
        $list = $this->menusService->getMenuList($data);
        return ReturnData::getInstance()->show($response, CodeResponse::SUCCESS, '', $list);
    }

    public function parentList(ResponseInterface $response)
    {
        $list = MenusService::getInstance()->getAllMenuList();
        return ReturnData::getInstance()->show($response, CodeResponse::SUCCESS, '', $list);
    }

    public function operate(RequestInterface $request, ResponseInterface $response)
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

    // 删除
    public function del(RequestInterface $request, ResponseInterface $response)
    {
        (new MenusValidate())->goCheck($this->validatorFactory, $request, 'del');
        $data = $request->all();
        Db::beginTransaction();
        try {
            $result = MenusService::getInstance()->del($data['menu_id']);
            $roleResult = RolesService::getInstance()->updateMenuIdList($data['menu_id']);
            // 如果没有异常，则提交事务
            Db::commit();
            if ($result && $roleResult) {
                return ReturnData::getInstance()->show($response, CodeResponse::MENUSDELSUCCESS, trans('messages.MenusDelSuccess'));
            }
            return ReturnData::getInstance()->show($response, CodeResponse::MENUSDELFAIL, trans('messages.MenusDelFail'));
        } catch (Exception $e) {
            // 如果捕获到异常，则回滚事务
            Db::rollBack();
        }
    }

    protected function add($data, $request, $response)
    {
        $data = Common::trimArr($data);
        (new MenusValidate())->goCheck($this->validatorFactory, $request, 'add');
        $result = $this->menusService->add($data);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::MENUSADDSUCCESS, trans('messages.MenusAddSuccess'));
        }
        return ReturnData::getInstance()->show($response, CodeResponse::MENUSADDFAIL, trans('messages.MenusAddFail'));
    }

    protected function edit($data, $request, $response)
    {
        $data = Common::trimArr($data);
        (new MenusValidate())->goCheck($this->validatorFactory, $request, 'edit');
        $result = $this->menusService->edit($data);
        if ($result) {
            return ReturnData::getInstance()->show($response, CodeResponse::MENUSEDITSUCCESS, trans('messages.MenusEditSuccess'));
        }
        return ReturnData::getInstance()->show($response, CodeResponse::MENUSEDITFAIL, trans('messages.MenusEditFail'));
    }
}
