<?php

namespace customs\CustomsDeclareClient\Base;

use customs\CustomsDeclareClient\Application;

/**
 * 身份验证.
 */
class Credential
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}
