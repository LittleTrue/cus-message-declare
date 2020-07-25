<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口撤销申请单.
 */
class CancelDeclareExportService
{
    /**
     * @var BankPurchase
     */
    private $_cancelDeclareExport;

    public function __construct(Application $app)
    {
        $this->_cancelDeclareExport = $app['cancel_declare_export'];
    }

    /**
     * 撤销申请单.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_cancelDeclareExport->declare($declareConfig, $declareParams);
    }
}
