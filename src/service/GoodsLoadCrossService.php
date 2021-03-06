<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 广州单一窗口 - KJ报文 - 进出口装载单申报.
 */
class GoodsLoadCrossService
{
    /**
     * @var GoodsLoadCross
     */
    private $_goodsLoadCrossClient;

    public function __construct(Application $app)
    {
        $this->_goodsLoadCrossClient = $app['goods_load_cross'];
    }

    /**
     * 获取对应服务的报文类型messageType.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function getMessageType()
    {
        return $this->_goodsLoadCrossClient->messageType;
    }

    /**
     * 进出口装载单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function generateXmlPost(array $infos)
    {
        if (empty($infos)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_goodsLoadCrossClient->generateXmlPost($infos);
    }
}
