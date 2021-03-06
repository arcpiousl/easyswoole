<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/1/22
 * Time: 下午12:51
 */

namespace EasySwoole\Core\Component\Rpc\AbstractInterface;


use EasySwoole\Core\Component\Rpc\Client\ResponseObj;
use EasySwoole\Core\Component\Rpc\Common\Status;
use EasySwoole\Core\Component\Spl\SplStream;
use EasySwoole\Core\Socket\AbstractInterface\TcpController;
use EasySwoole\Core\Socket\Client\Tcp;
use EasySwoole\Core\Socket\Common\CommandBean;

abstract class AbstractRpcService extends TcpController
{
    private $response;
    function __construct(Tcp $client, CommandBean $request, SplStream $response)
    {
        $this->response = new ResponseObj();
        parent::__construct($client, $request, $response);
    }

    protected function actionNotFound(?string $actionName)
    {
        $this->response()->setError("action : {$actionName} not found");
        $this->response()->setStatus(Status::ACTION_NOT_FOUND);
    }

    protected function onException(\Throwable $throwable,$actionName): void
    {
        $this->response()->setError($throwable->getMessage());
        $this->response()->setStatus(Status::SERVER_ERROR);
    }

    protected function response():ResponseObj
    {
        return $this->response;
    }

    protected function __hook(?string $actionName)
    {
        parent::__hook($actionName); // TODO: Change the autogenerated stub
        if(empty($this->response()->getStatus())){
            $this->response()->setStatus(Status::OK);
        }
        parent::response()->write($this->response->__toString());
    }
}