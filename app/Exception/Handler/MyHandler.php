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

namespace App\Exception\Handler;

use App\Exception\BaseException;
use App\helpers\Log;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class MyHandler extends ExceptionHandler
{
    private $httpCode;

    private $code;

    private $msg;

    #[Inject]
    protected Log $log;

    public function handle(Throwable $throwable, ResponsePlusInterface $response)
    {
        if ($throwable instanceof BaseException) {
            // 如果是自定义异常
            $this->httpCode = $throwable->httpCode;
            $this->msg = $throwable->msg;
            $this->code = $throwable->code;
        } else {
            $this->httpCode = 503;
            $this->msg = '内部错误';
            $this->code = 999;
            echo '';
            $this->log->logInfo('内部错误：' . $throwable->getMessage());
        }

        $result = [
            'msg' => $this->msg,
            'code' => $this->code,
        ];

        $response = $response->withHeader('Access-Control-Allow-Origin', '*'); // 或者具体的域名
        $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response = $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, Accept, X-Requested-With, Access-Control-Request-Method, Access-Control-Request-Headers');
        $response = $response->withHeader('Access-Control-Expose-Headers', '*');
        $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');

        return $response->withHeader('Content-Type', 'application/json')->withStatus($this->httpCode)->withBody(new SwooleStream(json_encode($result, 1)));
    }

    public function isValid(Throwable $throwable): bool
    {
        // TODO: Implement isValid() method.
        return true;
    }

    //    public function logInfo($message)
    //    {
    //        // 创建一个 Channel，参数 log 即为 Channel 的名字
    //        $log = new Logger('log');
    //
    //        // 创建两个 Handler，对应变量 $stream 和 $fire
    //        $stream = new StreamHandler('./runtime/logs/test.log');
    //        $fire = new FirePHPHandler();
    //
    //        // 定义时间格式为 "Y-m-d H:i:s"
    //        $dateFormat = 'Y n j, g:i a';
    //        // 定义日志格式为 "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
    //        $output = "%datetime%||%channel||%level_name%||%message%||%context%||%extra%\n";
    //        // 根据 时间格式 和 日志格式，创建一个 Formatter
    //        $formatter = new LineFormatter($output, $dateFormat);
    //
    //        // 将 Formatter 设置到 Handler 里面
    //        $stream->setFormatter($formatter);
    //
    //        // 将 Handler 推入到 Channel 的 Handler 队列内
    //        $log->pushHandler($stream);
    //        $log->pushHandler($fire);
    //
    //        $log->error($message);
    //    }
}
