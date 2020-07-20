<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口电子订单申报.
 */
class ElectronicOrderExportService
{
    /**
     * @var BankPurchase
     */
    private $_electronicOrderExport;

    public function __construct(Application $app)
    {
        $this->_electronicOrderExport = $app['electronic_order_export'];
    }

    /**
     * 出口电子订单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_electronicOrderExport->declare($declareConfig, $declareParams);
    }
}
