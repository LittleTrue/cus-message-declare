<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\OrderCross;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['order_cross'] = function ($app) {
            return new Client($app);
        };
    }
}
