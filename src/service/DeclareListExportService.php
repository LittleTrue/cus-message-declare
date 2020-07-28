<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 出口申报清单.
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
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        if (empty($declareConfig) || empty($declareParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_declareListExport->generateXmlPost($declareConfig, $declareParams);
    }

    /**
     * 生成Http报文
     */
    public function genarteDoc($messageType, $xml, $base)
    {
        return $this->_declareListExport->genarteDoc($messageType, $xml, $base);
    }
}
