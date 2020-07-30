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
     * @var TransportBillImport
     */
    private $_transportBillImport;

    public function __construct(Application $app)
    {
        $this->_transportBillImport = $app['transport_bill_import'];
    }

    /**
     * 获取对应服务的报文类型messageType.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function getMessageType()
    {
        return $this->_transportBillImport->messageType;
    }

    /**
     * 运单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $transportBase, array $transportParams)
    {
        if (empty($transportBase) || empty($transportParams)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_transportBillImport->generateXmlPost($transportBase, $transportParams);
    }
}
