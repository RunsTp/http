<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/24
 * Time: 下午11:10
 */

namespace EasySwoole\Http;


use EasySwoole\Component\Invoker;
use EasySwoole\Http\Message\Status;

class WebService
{
    private $dispatcher;
    private $exceptionHandler = null;
    final function __construct($controllerNameSpace = 'App\\HttpController\\',$depth = 5)
    {
        $this->dispatcher = new Dispatcher($controllerNameSpace,$depth);
    }

    function setExceptionHandler(callable $handler = null)
    {
        $this->exceptionHandler = $handler;
    }

    function onRequest(Request $request_psr,Response $response_psr):void
    {
        try{
            $this->dispatcher->dispatch($request_psr,$response_psr);
        }catch (\Throwable $throwable){
            if($this->exceptionHandler){
                Invoker::callUserFunc($this->exceptionHandler,$throwable,$request_psr,$response_psr);
            }else{
                $response_psr->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
                $response_psr->write(nl2br($throwable->getMessage() ."\n". $throwable->getTraceAsString()));
            }
        }
        $response_psr->response();
    }
}