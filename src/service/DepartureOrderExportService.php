<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口离境单.
 */
class DepartureOrderExportService
{
    /**
     * @var BankPurchase
     */
    private $_departureOrderExport;

    public function __construct(Application $app)
    {
        $this->_departureOrderExport = $app['departure_order_export'];
    }

    /**
     * 出口离境单.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_departureOrderExport->declare($declareConfig, $declareParams);
    }
}
