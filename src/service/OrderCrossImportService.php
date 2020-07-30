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
    public function generateXmlPost(array $declareConfig, $xmlData, $messageType)
    {
        if (empty($declareConfig) || empty($xmlData)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_orderCrossImportClient->generateXmlPost($declareConfig, $declareParams);
    }
}
