<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 广州单一窗口 - 单一窗口HTTP申报通路报文封装.
 */
class HttpMessageDeclareService
{
    /**
     * @var HttpMessageDeclare
     */
    private $_httpMessageDeclareClient;


    public function __construct(Application $app)
    {
        $this->_httpMessageDeclareClient = $app['http_message_declare'];
    }

    /**
     * 生成按照单一窗口HTTP申报通路封装的报文, 传入key则调用依赖完成RSA加签操作.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generatePublicHttpMessage(array $baseConfig, $xmlData, $messageType, $key = '')
    {
        if (empty($baseConfig) || empty($xmlData) || empty($messageType)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_httpMessageDeclareClient->generateHttpDoc($messageType, $xmlData, $baseConfig, $key);
    }
}
