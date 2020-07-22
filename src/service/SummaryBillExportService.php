<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口汇总单.
 */
class SummaryBillExportService
{
    /**
     * @var BankPurchase
     */
    private $_summaryBillExport;

    public function __construct(Application $app)
    {
        $this->_summaryBillExport = $app['summary_bill_export'];
    }

    /**
     * 出口汇总单.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_summaryBillExport->declare($declareConfig, $declareParams);
    }
}
