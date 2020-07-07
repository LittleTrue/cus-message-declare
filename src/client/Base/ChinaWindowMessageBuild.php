<?php

namespace customs\CustomsDeclareClient\Base;

/**
 * Trait ChinaWindowMessageBuild.
 */
trait ChinaWindowMessageBuild
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
    protected $receiver = 'E010000';

    /**
     * 生成报文根节点,dom操作类.
     */
    public function setRootNode()
    {
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Package');
        $this->dom->appendchild($root_node);

        //文档ceb根结点, 根节点和对应的节点用数组存储
        $this->nodeLink['Package'] = $root_node;

        return true;
    }

    /**
     * 生成基础报文头.
     */
    public function setEnvelopInfo($messageId, $senderId)
    {
        if (empty($this->dom) || empty($this->nodeLink['Package'])) {
            return ['status' => false, 'message' => 'dom根节点或头结点不存在，请先建立根节点'];
        }

        if (empty($this->sendTime) || empty($this->sendTime)) {
            return ['status' => false, 'message' => '申报时间异常。'];
        }

        $declare_head = [
            'message_id'  => $messageId,
            'file_name'   => $messageId . $this->messageType,
            'messageType' => $this->messageType,
            'sender_id'   => $senderId,
            'receiver'    => $this->receiver,
            'send_time'   => date('Y-m-dTH:i:s', $this->sendTime),
            'version'     => $this->version,
        ];

        $envelopInfo = $this->dom->createElement('EnvelopInfo');
        $this->nodeLink['Package']->appendchild($envelopInfo);

        $this->dom = $this->createEle($declare_head, $this->dom, $envelopInfo);

        $this->nodeLink['EnvelopInfo'] = $envelopInfo;

        return  true;
    }

    /**
     * 设置报文基础结构
     * SignedData:未知作用的结点
     * Data:单票业务数据，待加签内容
     * ---- 以下两个未知作用:
     * SignerInfo:进行加签的证书号
     * HashSign:对Data节点.
     */
    public function setDataStructure()
    {
        if (empty($this->dom) || empty($this->nodeLink['Package'])) {
            return ['status' => false, 'message' => 'dom根节点或头结点不存在，请先建立根节点'];
        }

        $data_info = $this->dom->createElement('DataInfo');
        $this->nodeLink['Package']->appendchild($data_info);

        $this->nodeLink['DataInfo'] = $data_info;

        $signed_data = $this->dom->createElement('SignedData');
        $this->nodeLink['DataInfo']->appendchild($signed_data);

        $this->nodeLink['SignedData'] = $signed_data;

        $data = $this->dom->createElement('Data');

        $this->nodeLink['SignedData']->appendchild($data);

        $this->nodeLink['Data'] = $data;

        return ['status' => true];
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
