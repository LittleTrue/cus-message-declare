<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\HttpMessageDeclare;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['http_message_declare'] = function ($app) {
            return new Client($app);
        };
    }
}
