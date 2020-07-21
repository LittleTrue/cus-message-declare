<?php

namespace customs\CustomsDeclareClient\CebMessage\DepartureOrderExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['departure_order_export'] = function ($app) {
            return new Client($app);
        };
    }
}