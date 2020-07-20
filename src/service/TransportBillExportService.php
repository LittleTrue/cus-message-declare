<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口运单申报.
 */
class TransportBillExportService
{
    /**
     * @var BankPurchase
     */
    private $_transportBillExport;

    public function __construct(Application $app)
    {
        $this->_transportBillExport = $app['transport_bill_export'];
    }

    /**
     * 出口运单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_transportBillExport->declare($declareConfig, $declareParams);
    }
}
