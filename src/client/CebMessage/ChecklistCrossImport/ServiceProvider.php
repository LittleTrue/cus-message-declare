<?php

namespace customs\CustomsDeclareClient\CebMessage\ChecklistCrossImport;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Class ServiceProvider.
 */
class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['checklist_cross_import'] = function ($app) {
            return new Client($app);
        };
    }
}
