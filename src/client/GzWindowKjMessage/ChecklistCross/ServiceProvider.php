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
        $app['checklist_cross'] = function ($app) {
            return new Client($app);
        };
    }
}
