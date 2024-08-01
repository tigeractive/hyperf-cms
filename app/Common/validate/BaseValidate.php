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

namespace App\Common\validate;

use App\Exception\ParameterException;
use App\helpers\Common;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class BaseValidate
{
    protected $rule = [
    ];

    protected $message = [
    ];

    protected $scene = [
    ];

    protected $currentScene;

    protected $error = [];

    /**
     * 场景需要验证的规则.
     * @var array
     */
    protected $presentRule = [];

    protected $tmpRule = [];

    public function scene($name)
    {
        $this->currentScene = $name;
        return $this;
    }

    // 获取错误信息
    public function getError()
    {
        return $this->error;
    }

    public function goCheck($validatorFactory, RequestInterface $request, $scene = '')
    {
        if ($this->getScence($scene)) {
            if (! empty($this->tmpRule)) {
                $newRule = [];
                foreach ($this->tmpRule as $key => $val) {
                    if (str_contains($val, '|')) {
                        $val = explode('|', $val);
                        foreach ($val as $k => $v) {
                            if (str_contains($v, '.')) {
                                $this->assembleRule($v);
                            }
                        }
                    } elseif (str_contains($val, '.')) {
                        $rule = explode('.', $val);
                        $this->assembleRule($val);
                    } else {
                        if (array_key_exists($val, $this->rule)) {
                            $this->presentRule[$val] = $this->rule[$val];
                        }
                    }
                }
            }
        } else {
            $this->presentRule = $this->rule;
        }

        $data = Common::trimArr($request->all());

        $validator = $validatorFactory->make($data, $this->presentRule, $this->message);

        if ($validator->fails()) {
            throw new ParameterException([
                'msg' => implode(' ', $validator->errors()->all()),
            ]);
        }

        return true;
    }

    protected function getScence($scene = ''): bool
    {
        if (empty($scene)) {
            return false;
        }

        $this->tmpRule = [];
        $scene = $this->scene[$scene];
        if (isset($scene) && is_string($scene)) {
            $scene = explode(',', $scene);
        }
        $this->tmpRule = $scene;
        return true;
    }

    protected function assembleRule($rule)
    {
        $rule = explode('.', $rule);
        if (array_key_exists($rule[0], $this->rule)) {
            if (isset($this->presentRule[$rule[0]])) {
                $this->presentRule[$rule[0]] = $this->presentRule[$rule[0]] . '|' . $rule[1];
            } else {
                $this->presentRule[$rule[0]] = $rule[1];
            }
        }
    }
}
