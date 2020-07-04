<?php

namespace customs\CustomsDeclareClient\Base;

use customs\CustomsDeclareClient\Application;

/**
 * 底层请求.
 */
class BaseClient
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $json = [];

    /**
     * @var string
     */
    protected $language = 'zh-cn';

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * 获取特定位数时间戳.
     * @return int
     */
    public function getTimestamp(int $digits = 10)
    {
        $digits = $digits > 10 ? $digits : 10;

        $digits = $digits - 10;

        if ((!$digits) || (10 == $digits)) {
            return time();
        }

        return number_format(microtime(true), $digits, '', '') - 50000;
    }

    /**
     * 获取报文流水号.
     * @return string
     */
    protected function generateMessageId()
    {
        return date('ymd') . substr(substr(microtime(), 2, 6)
        * time(), 2, 6) . mt_rand(1000, 9999);
    }
}
