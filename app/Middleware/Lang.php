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

use App\CodeResponse;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Lang implements MiddlewareInterface
{
    #[Inject]
    private TranslatorInterface $translator;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        // 自动侦测设置获取语言选择
        $langSet = '';
        if ($request->getQueryParams(CodeResponse::LANG)) {
            $langSet = $request->getQueryParams(CodeResponse::LANG);
        } elseif ($request->getHeader(CodeResponse::LANG)) {
            $langSet = $request->getHeader(CodeResponse::LANG);
        } elseif ($request->getCookieParams(CodeResponse::LANG)) {
            $langSet = $request->getCookieParams(CodeResponse::LANG);
        } elseif ($request->getServerParams('HTTP_ACCEPT_LANGUAGE')) {
            $langSet = $request->getServerParams('HTTP_ACCEPT_LANGUAGE');
        }
        if (! empty($langSet[0])) {
            $this->translator->setLocale($langSet[0]);
        }

        return $handler->handle($request);
    }
}
