<?php

namespace customs\CustomsDeclareClient\CebMessage\OrderCrossImport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['order_cross_import'] = function ($app) {
            return new Client($app);
        };
    }
}
