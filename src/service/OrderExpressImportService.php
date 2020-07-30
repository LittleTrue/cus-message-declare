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
     * @var OrderExpressImport
     */
    private $_orderExpressImportClient;

    public function __construct(Application $app)
    {
        $this->_orderExpressImportClient = $app['order_express_import'];
    }

    /**
     * 获取对应服务的报文类型messageType.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function getMessageType()
    {
        return $this->_orderExpressImportClient->messageType;
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
