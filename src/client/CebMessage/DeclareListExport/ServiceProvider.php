<?php

namespace customs\CustomsDeclareClient\CebMessage\DeclareListExport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['declare_list_export'] = function ($app) {
            return new Client($app);
        };
    }
}