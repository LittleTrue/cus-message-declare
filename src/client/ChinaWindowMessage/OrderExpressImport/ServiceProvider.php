<?php

namespace customs\CustomsDeclareClient\ChinaWindowMessage\OrderExpressImport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['order_express_import'] = function ($app) {
            return new Client($app);
        };
    }
}
