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
     * @var BankPurchase
     */
    private $_orderCrossImportClient;

    public function __construct(Application $app)
    {
        $this->_orderCrossImportClient = $app['order_cross_import'];
    }

    /**
     * 进口订单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_orderCrossImportClient->declare($declareConfig, $declareParams);
    }
}
