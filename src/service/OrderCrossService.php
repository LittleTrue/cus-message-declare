<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 广州单一窗口 - KJ报文 - 进出口订单申报.
 */
class OrderCrossService
{
    /**
     * @var BankPurchase
     */
    private $_orderCrossClient;

    public function __construct(Application $app)
    {
        $this->_orderCrossClient = $app['order_cross'];
    }

    /**
     * 进出口订单申报.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function declare(array $infos)
    {
        if (empty($infos)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_orderCrossClient->declare($infos);
    }
}
