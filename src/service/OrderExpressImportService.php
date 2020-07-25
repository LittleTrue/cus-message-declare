<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 中国单一窗口 - 快件报文 - 进口快件订单申报.
 */
class OrderExpressImportService
{
    /**
     * @var BankPurchase
     */
    private $_orderExpressImportClient;

    public function __construct(Application $app)
    {
        $this->_orderExpressImportClient = $app['order_express_import'];
    }

    /**
     * 进口快件订单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $infos)
    {
        if (empty($infos)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_orderExpressImportClient->generateXmlPost($infos);
    }
}
