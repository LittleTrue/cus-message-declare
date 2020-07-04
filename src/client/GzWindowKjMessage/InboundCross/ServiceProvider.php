<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\InboundCross;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['inbound_cross'] = function ($app) {
            return new Client($app);
        };
    }
}
