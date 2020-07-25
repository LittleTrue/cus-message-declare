<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口收款单申报.
 */
class PayReceiveCrossExportService
{
    /**
     * @var BankPurchase
     */
    private $_payReceiveCrossExportClient;

    public function __construct(Application $app)
    {
        $this->_payReceiveCrossExportClient = $app['pay_receive_cross_export'];
    }

    /**
     * 出口收款单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_payReceiveCrossExportClient->generateXmlPost($declareConfig,$declareParams);
    }
}
