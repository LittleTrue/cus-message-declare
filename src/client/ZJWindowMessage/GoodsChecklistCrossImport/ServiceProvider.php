<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\ChecklistCross;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['zj_goods_checklist_import'] = function ($app) {
            return new Client($app);
        };
    }
}
