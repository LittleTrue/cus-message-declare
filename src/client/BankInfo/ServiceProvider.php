<?php

namespace customs\CrossDeclareOrder\BankInfo;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['bank_info'] = function ($app) {
            return new Client($app);
        };
    }
}
