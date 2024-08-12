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

namespace App\Common\rule;

use App\Common\service\UsersService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\Event\ValidatorFactoryResolved;
use Hyperf\Validation\Validator;

#[Listener]
class
UserUpdateUniqueListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            ValidatorFactoryResolved::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var ValidatorFactoryInterface $validatorFactory */
        $validatorFactory = $event->validatorFactory;
        // 注册了 foo 验证器
        $validatorFactory->extend('updateunique', function (
            string $attribute,
            mixed $value,
            array $parameters,
            Validator $validator
        ): bool {
            var_dump($value);
            $username = $value;
            $userId = $validator->getData()['user_id'];
            $result = UsersService::getInstance()->getUserByNameNotId($userId, $username);
            if ($result) {
                return false;
            }
            return true;
        });

    }
}
