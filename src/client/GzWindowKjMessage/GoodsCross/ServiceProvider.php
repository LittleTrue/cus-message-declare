<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\GoodsCross;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * 客户端 -- 由于业务后续修改为走总署版, 目前暂时不集成, 等待业务需要再进行集成.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['goods_cross'] = function ($app) {
            return new Client($app);
        };
    }
}
