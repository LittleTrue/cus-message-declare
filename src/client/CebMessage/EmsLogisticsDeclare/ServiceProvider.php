<?php

namespace customs\CustomsDeclareClient\CebMessage\EmsLogisticsDeclare;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['ems_logistics_declare'] = function ($app) {
            return new Client($app);
        };
    }
}