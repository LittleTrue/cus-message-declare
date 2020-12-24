<?php

namespace customs\CustomsDeclareClient\CebMessage\SFLogisticsDeclare;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['sf_logistics_declare'] = function ($app) {
            return new Client($app);
        };
    }
}