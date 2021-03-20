<?php
namespace customs\CustomsDeclareClient\Base;

/**
 * Trait JxImportMessageBuild
 */
trait JxImportMessageBuild
{
    /**
     * @var object dom节点链对象
     */
    public $dom;

    /**
     * @var array nodeLink节点集合
     */
    public $nodeLink = [];

    /**
     * @var string version 报文版本号
     */
    public $version = '1.0.0';

    public function setRootNode($type)
    {
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('mo');
        $this->dom->appendchild($root_node);
        $root_node->setAttribute('version', $this->version);

        //生成head
        $head = $this->dom->createElement('head');
        $root_node->appendchild($head);
        $businessType = $this->dom->createElement('businessType');
        $head->appendchild($businessType);
        $zhi = $this->dom->createTextNode($type);
        $businessType->appendchild($zhi);

        //生成body
        $body = $this->dom->createElement('body');
        $root_node->appendchild($body);

        $this->nodeLink['body'] = $body;

        return true;
    }

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
