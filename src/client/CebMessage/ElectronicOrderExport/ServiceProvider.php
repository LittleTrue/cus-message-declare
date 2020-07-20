<?php

namespace customs\CustomsDeclareClient\CebMessage\ElectronicOrderExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['electronic_order_export'] = function ($app) {
            return new Client($app);
        };
    }
}