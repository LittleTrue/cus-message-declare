<?php

namespace customs\CustomsDeclareClient\Base;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        //注册验证器
        $app['credential'] = function ($app) {
            return new Credential($app);
        };
    }
}
