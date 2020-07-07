<?php

namespace customs\CustomsDeclareClient\Base;

use GuzzleHttp\Psr7\Response;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * Trait GzWindowKjMessageBuild.
 */
trait GzWindowKjMessageBuild
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


    //生成含值的节点
    public function createEle($element, $dom, $parents)
    {
        foreach ($element as $key => $value) {
            $note = $dom->createElement($key);
            $parents->appendchild($note);
            $zhi = $dom->createTextNode($value);
            $note->appendchild($zhi);
        }
        return $dom;
    }
}
