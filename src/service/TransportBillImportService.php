<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 海关直连总署 - CEB报文 - 进口运单申报.
 */
class TransportBillImportService
{
    /**
     * @var BankPurchase
     */
    private $_transportBillImport;

    public function __construct(Application $app)
    {
        $this->_transportBillImport = $app['transport_bill_import'];
    }

    /**
     * 运单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function transportDeclare(array $transportBase, array $transportParams)
    {
        // if (empty($declareConfig) || empty($declareParams)) {
        //     throw new ClientError('参数缺失', 1000001);
        // }

        return $this->_transportBillImport->transportDeclare($transportBase, $transportParams);
    }
}
