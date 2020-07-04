<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 *  广州单一窗口 - KJ报文 - 进出口商品申报.
 */
class GoodsCrossService
{
    /**
     * @var BankPurchase
     */
    private $_goodsCrossClient;

    public function __construct(Application $app)
    {
        $this->_goodsCrossClient = $app['goods_cross'];
    }

    /**
     * 进出口商品申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $infos)
    {
        if (empty($infos)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_goodsCrossClient->declare($infos);
    }
}
