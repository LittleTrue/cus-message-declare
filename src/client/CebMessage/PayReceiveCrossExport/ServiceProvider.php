<?php

namespace customs\CustomsDeclareClient\CebMessage\PayReceiveCrossExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['pay_receive_cross_export'] = function ($app) {
            return new Client($app);
        };
    }
}
