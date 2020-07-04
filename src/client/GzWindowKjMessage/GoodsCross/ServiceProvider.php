<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\GoodsCross;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
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
