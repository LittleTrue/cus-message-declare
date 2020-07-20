<?php

namespace customs\CustomsDeclareClient\CebMessage\TransportBillExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['transport_bill_export'] = function ($app) {
            return new Client($app);
        };
    }
}