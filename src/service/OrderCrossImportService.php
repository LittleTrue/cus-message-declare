<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 进口订单申报.
 */
class OrderCrossImportService
{
    /**
     * @var OrderCrossImport
     */
    private $_orderCrossImportClient;

    public function __construct(Application $app)
    {
        $this->_orderCrossImportClient = $app['order_cross_import'];
        $this->_httpMessageDeclareClient = $app['http_message_declare'];
    }

    /**
     * 获取对应服务的报文类型messageType.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function getMessageType()
    {
        return $this->_orderCrossImportClient->messageType;
    }

    /**
     * 进口订单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_orderCrossImportClient->generateXmlPost($declareConfig, $declareParams);
    }

    /**
     * 生成按照单一窗口HTTP申报通路封装的报文.
     */
    public function generateHttpDoc(array $declareConfig, array $declareParams, array $httpBase, $key = '')
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        if (empty($httpBase)) {
            throw new ClientError('参数缺失', 1000001);
        }

        $xml_data = $this->_orderCrossImportClient->generateXmlPost($declareConfig, $declareParams);

        return $this->_httpMessageDeclareClient->generateHttpDoc($this->_orderCrossImportClient->messageType, $xml_data, $httpBase, $key);
    }
}

