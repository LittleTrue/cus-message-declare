<?php

namespace customs\CustomsDeclareClient\SignMessage\Rsa;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['rsa'] = function ($app) {
            return new Client($app);
        };
    }
}
