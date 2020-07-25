<?php

namespace customs\CustomsDeclareClient\CebMessage\ElectronicOrderExport;

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
    public $messageType = 'CEB303Message';

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
     * 电子订单数据.
     */
    public function generateXmlPost($declareConfig, $declareParams)
    {
        $rule = [
            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',

            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',

            'EBPEntNo' => 'require|max:50',
            'EBPEntName' => 'require|max:100',
            'EBEntNo' => 'require|max:18',
            'EBEntName' => 'require|max:100',
        ];

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        $this->sendTime = date('YmdHis', time());
        $this->opType   = $declareConfig['OpType'];

        //根节点生成--父类
        $this->setRootNode($declareConfig['MessageId']);

        //一个报文可以又多个订单
        foreach ($declareParams as $key => $value) {
            $order_head = $value['order_head'];

            $Order = $this->dom->createElement('ceb:Order');
            $this->nodeLink['root_node']->appendchild($Order);
            $this->nodeLink['order'] = $Order;

            $OrderListEle_arr = [];

            //一个订单一个订单头
            $OrderHead = $this->dom->createElement('ceb:OrderHead');
            $this->nodeLink['order']->appendchild($OrderHead);

            $OrderHeadEle = [
                'ceb:guid'      => $declareConfig['MessageId'],
                'ceb:appType'   => $this->opType,
                'ceb:appTime'   => $this->sendTime,
                'ceb:appStatus' => 2,
                'ceb:orderType' => 'I',
                'ceb:orderNo'   => $order_head['EntOrderNo'],

                'ceb:ebpCode' => $declareConfig['EBPEntNo'],
                'ceb:ebpName' => $declareConfig['EBPEntName'],
                'ceb:ebcCode' => $declareConfig['EBEntNo'],
                'ceb:ebcName' => $declareConfig['EBEntName'],

                'ceb:goodsValue' => $order_head['OrderGoodTotal'],
                'ceb:freight'    => $order_head['Freight'],
                'ceb:currency'   => $this->currency,
                'ceb:note'       => '',
            ];

            $this->dom = $this->createEle($OrderHeadEle, $this->dom, $OrderHead);

            //商品信息
            foreach ($value['list'] as $kk => $vv) {
                $OrderList = $this->dom->createElement('ceb:OrderList');
                $this->nodeLink['order']->appendchild($OrderList);

                $OrderListEle = [
                    'ceb:gnum'         => $kk + 1,
                    'ceb:itemNo'       => $vv['SKU'],
                    'ceb:itemName'     => $vv['GoodsName'],
                    'ceb:itemDescribe' => $vv['GoodsName'],
                    'ceb:barCode'      => $vv['BarCode'],
                    'ceb:unit'         => $vv['GUnit'], //成交计量单位
                    'ceb:currency'     => $this->currency,
                    'ceb:country'      => $vv['OriginCountry'],
                    'ceb:qty'          => $vv['GoodsNumber'],
                    'ceb:price'        => (float) $vv['GoodsPrice'],
                    'ceb:totalPrice'   => round((float) $vv['GoodsPrice'] * $vv['GoodsNumber'], 2),
                    'ceb:note'         => '',
                ];

                $this->dom = $this->createEle($OrderListEle, $this->dom, $OrderList);

                $OrderListEle_arr[] = $OrderListEle;
            }

            //验证数据
            $this->checkOrderInfo($key+1,$OrderHeadEle, $OrderListEle_arr);
        }

        //统一传输实体结点实现--父类
        $BaseTransferEle = [
            'copCode' => $declareConfig['DeclEntNo'],
            'copName' => $declareConfig['DeclEntName'],
            'dxpMode' => 'DXP',
            'dxpId'   => $declareConfig['DeclEntDxpid'],
            'note'    => '',
        ];

        $this->setBaseTransfer($BaseTransferEle);

        return $this->dom->saveXML();
    }

    /**
     * 定义验证器来校验订单信息
     * .
     */
    public function checkOrderInfo($kk,$order_head_ele, $order_list_ele_arr)
    {
        $this->credentialValidate->setRule([
            'ceb:orderNo'   => 'require|max:60',

            'ceb:ebpCode' => 'require|max:50',
            'ceb:ebpName' => 'require|max:100',
            'ceb:ebcCode' => 'require|max:18',
            'ceb:ebcName' => 'require|max:100',

            'ceb:goodsValue' => 'require',
            'ceb:freight'    => 'require',
        ]);
  
        if (!$this->credentialValidate->check($order_head_ele)) {
            throw new ClientError('第'.$kk.'条'.'报文订单数据: ' . $this->credentialValidate->getError());
        }

        $goods_total = 0;
        //检验商品, 并且计算总价是否相符
        foreach ($order_list_ele_arr as $key => $value) {

            $goods_total += ($value['ceb:qty'] * (float) $value['ceb:price']);
        }

        if (!($this->floatCmp((string) round($goods_total, 2), (string) $order_head_ele['ceb:goodsValue']))) {
            throw new ClientError('第'.$kk.'条'.'报文订单数据: 商品实际成交价与订单记录不符。');
        }

        return true;
    }
}
