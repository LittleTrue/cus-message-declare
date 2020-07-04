<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\GoodsLoadCross;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['goods_load_cross'] = function ($app) {
            return new Client($app);
        };
    }
}
