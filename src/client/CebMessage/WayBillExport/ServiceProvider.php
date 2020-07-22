<?php

namespace customs\CustomsDeclareClient\CebMessage\WayBillExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['way_bill_export'] = function ($app) {
            return new Client($app);
        };
    }
}