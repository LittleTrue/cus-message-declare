<?php

namespace customs\CustomsDeclareClient\Base;

use GuzzleHttp\Psr7\Response;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * Trait ZJWindowMessageBuild.
 * 浙江单一窗口对接基础组件类.
 */
trait ZJWindowMessageBuild
{
    /**
     * @var object 币制代码默认
     */
    public $currency = '142';

    /**
     * @var object 报文版本
     */
    protected $version = '3.0';

    /**
     * @var object 报文接受者标志
     */
    protected $receiver = 'KJPUBLIC';


}
