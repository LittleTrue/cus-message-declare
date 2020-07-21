<?php

namespace customs\CustomsDeclareClient\CebMessage\CancelDeclareExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['cancel_declare_export'] = function ($app) {
            return new Client($app);
        };
    }
}