<?php

namespace customs\CustomsDeclareClient\CebMessage\SFLogisticsDeclare;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\CebMessageBuild;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    use CebMessageBuild;

    //本报文编号
    public $messageType = '';

    /**
     * @var Application
     */
    protected $credentialValidate;

    //报文发送时间
    private $sendTime;

    //操作类型
    private $opType;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['credential'];
    }

    /**
     * 出口运单申报.
     */
    public function generateXmlPost($declareConfig, $declareParams)
    {
        $rule = [
            'head'         => 'require',
            'opt_type'     => 'require',
        ];

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        switch ($declareConfig['opt_type']) {
            case 'logistics_declare':
                $result = $this->generateFreight($declareConfig, $declareParams);
                break;
            case 'logistics_check':
                $result = $this->freightCheck($declareConfig, $declareParams);
                break;
            case 'logistics_cancel':
                $result = $this->freightCancel($declareConfig, $declareParams);
                break;
            default:
                throw new ClientError('报文组装操作类型错误');
                break;
        }

        return $result;
    }

    /**
     * 定义验证器来校验清单和清单商品信息.
     */
    public function checkInfo($LogisticsEle)
    {
        $rules = [
            
        ];

        $this->credentialValidate->setRule($rules);

        if (!$this->credentialValidate->check($LogisticsEle)) {
            throw new ClientError('运单数据: ' . $this->credentialValidate->getError());
        }

        return true;
    }

    //生成进口运单申报报文
    public function generateFreight($declareConfig, $declareParams)
    {
        //根节点生成--父类
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Request');
        $root_node->setAttribute('service','apiOrderService');
        $root_node->setAttribute('lang','zh_CN');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);
        $zhi = $this->dom->createTextNode($declareConfig['head']);
        $head->appendchild($zhi);

        $body_node = $this->dom->createElement('Body');
        $this->nodeLink['root_node']->appendchild($body_node);

        $order_node = $this->dom->createElement('Order');
        $body_node->appendchild($order_node);

        // 订单信息作为节点属性
        foreach ($declareParams as $key => $value) {
            if ('Cargo' == $key) {
                continue;
            }

            $order_node->setAttribute($key, $value);
        }

        foreach ($declareParams['Cargo'] as $goods) {
            $Cargo = $this->dom->createElement('Cargo');
            $order_node->appendchild($Cargo);

            // 商品节点添加属性
            foreach ($goods as $k => $v) {
                $Cargo->setAttribute($k, $v);
            }
        }

        return $this->dom->saveXML();
    }

    /**
     * 查询运单申报状态
     */
    public function freightCheck($declareConfig, $declareParams)
    {
        //根节点生成--父类
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Request');
        $root_node->setAttribute('service','OrderSearchService');
        $root_node->setAttribute('lang','zh_CN');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);
        $zhi = $this->dom->createTextNode($declareConfig['head']);
        $head->appendchild($zhi);

        $body_node = $this->dom->createElement('Body');
        $this->nodeLink['root_node']->appendchild($body_node);

        $order_node = $this->dom->createElement('OrderSearch');
        $body_node->appendchild($order_node);

        foreach ($declareParams as $key => $value) {
            $order_node->setAttribute($key, $value);
        }

        return $this->dom->saveXML();
    }

    /**
     * 运单申报取消
     */
    public function freightCancel($declareConfig, $declareParams)
    {
        //根节点生成--父类
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Request');
        $root_node->setAttribute('service','CancelOrderService');
        $root_node->setAttribute('lang','zh_CN');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);
        $zhi = $this->dom->createTextNode($declareConfig['head']);
        $head->appendchild($zhi);

        $body_node = $this->dom->createElement('Body');
        $this->nodeLink['root_node']->appendchild($body_node);

        $order_node = $this->dom->createElement('CancelOrder');
        $body_node->appendchild($order_node);

        foreach ($declareParams as $key => $value) {
            $order_node->setAttribute($key, $value);
        }

        return $this->dom->saveXML();
    }
}
