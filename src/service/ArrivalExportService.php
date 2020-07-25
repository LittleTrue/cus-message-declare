<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口运抵单.
 */
class ArrivalExportService
{
    /**
     * @var BankPurchase
     */
    private $_arrivalExport;

    public function __construct(Application $app)
    {
        $this->_arrivalExport = $app['arrival_export'];
    }

    /**
     * 出口运抵单.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_arrivalExport->generateXmlPost($declareConfig, $declareParams);
    }
}
