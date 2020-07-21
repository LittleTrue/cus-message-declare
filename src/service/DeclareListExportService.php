<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 进口运单申报.
 */
class DeclareListExportService
{
    /**
     * @var BankPurchase
     */
    private $_declareListExport;

    public function __construct(Application $app)
    {
        $this->_declareListExport = $app['declare_list_export'];
    }

    /**
     * 出口申报清单.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_declareListExport->declare($declareConfig, $declareParams);
    }
}
