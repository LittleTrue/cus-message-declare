<?php

namespace customs\CustomsDeclareClient\Base;

/**
 * Trait CebMessageBuild.
 */
trait CebMessageBuild
{
    /**
     * @var array 根节点操作和子类共享数组
     */
    public $nodeLink = [];

    /**
     * @var object dom节点链对象
     */
    public $dom;

    /**
     * @var object 币制代码默认
     */
    public $currency = '142';

    /**
     * @var object 报文版本
     */
    protected $version = '1.0';

    /**
     * @var object 报文接受者标志
     */
    protected $receiver = 'KJPUBLIC';

    /**
     * 生成报文根节点,dom操作类.
     */
    public function setRootNode($guid)
    {
        if (empty($this->messageType)) {
            throw new ClientError('报文类型为空', 1000000);
        }

        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('ceb:' . $this->messageType);
        $this->dom->appendchild($root_node);

        $root_node->setAttribute('guid', $guid);
        $root_node->setAttribute('version', $this->version);
        $root_node->setAttribute('xmlns:ceb', 'http://www.chinaport.gov.cn/ceb');
        $root_node->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        //文档ceb根结点, 根节点和对应的节点用数组存储
        $this->nodeLink['root_node'] = $root_node;

        return true;
    }

    /**
     * 生成基础报文传输实体节点.
     * @param [array] $enterprise [企业信息，需要按照参数$warning_tips的格式组装数组]
     */
    public function setBaseTransfer($enterprise)
    {
        if (empty($this->dom) || empty($this->nodeLink['root_node'])) {
            throw new ClientError('dom根节点或头结点不存在，请先建立根节点', 1000001);
        }

        $warning_tips = [
            'ceb:copCode' => $enterprise['copCode'],
            'ceb:copName' => $enterprise['copName'],
            'ceb:dxpMode' => $enterprise['dxpMode'],
            'ceb:dxpId'   => $enterprise['dxpId'],
        ];

        $base_transfer = $this->dom->createElement('ceb:BaseTransfer');
        $this->nodeLink['root_node']->appendchild($base_transfer);

        $this->dom = $this->createEle($warning_tips, $this->dom, $base_transfer);

        $this->nodeLink['base_transfer'] = $base_transfer;

        return true;
    }

    /**
     * 生成基础回执订阅实体节点（非必填）.
     */
    public function setBaseSubscribe($dom)
    {
        if (empty($this->dom) || empty($this->nodeLink['root_node'])) {
            throw new ClientError('dom根节点或头结点不存在，请先建立根节点', 1000002);
        }

        $base_subscribe = $this->dom->createElement('ceb:BaseSubscribe');
        $this->nodeLink['root_node']->appendchild($base_subscribe);
        $this->nodeLink['base_subscribe'] = $base_subscribe;

        return true;
    }

    /**
     * 生成签名节点（非必填）.
     */
    public function setSignature($dom)
    {
        if (empty($this->dom) || empty($this->nodeLink['root_node'])) {
            throw new ClientError('dom根节点或头结点不存在，请先建立根节点', 1000003);
        }

        $signature = $this->dom->createElement('ceb:Signature');
        $this->nodeLink['root_node']->appendchild($signature);
        $this->nodeLink['signature'] = $signature;

        return true;
    }

    /**
     * 生成扩展自定义数据实体节点（非必填）.
     * @param [type] $dom [description]
     */
    public function setExtendMessage($dom)
    {
        if (empty($this->dom) || empty($this->nodeLink['root_node'])) {
            throw new ClientError('dom根节点或头结点不存在，请先建立根节点', 1000004);
        }

        $extend_message = $this->dom->createElement('ceb:ExtendMessage');
        $this->nodeLink['root_node']->appendchild($extend_message);
        $this->nodeLink['extend_message'] = $extend_message;

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
