<?php

namespace customs\CustomsDeclareClient\CebMessage\OrderCrossImport;

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
    public $messageType = 'CEB311Message';

    /**
     * @var Application
     */
    protected $credentialValidate;

    //操作类型
    private $opType;

    //报文发送时间
    private $sendTime;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['credential'];
    }

    /**
     * 进口订单申报.
     *
     * @throws ClientError
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        $this->credentialValidate->setRule(
            [
                'MessageId' => 'require|max:36',

                'EBPEntNo'  => 'require|max:50',
                'EBPEntName' => 'require|max:100',

                'EBEntNo'    => 'require|max:18',
                'EBEntName'   => 'require|max:100',

                'DeclEntNo'   => 'require|max:18',
                'DeclEntName' => 'require|max:100',

                'DeclEntDxpid' => 'require|max:30',
                'OpType'       => 'require|max:1',
            ]
        );

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        //根节点生成--父类
        $this->setRootNode($declareConfig['MessageId']);

        $orderInfo = $declareParams['order_info'];
        $goodsInfo = $declareParams['goods_info'];

        $Order = $this->dom->createElement('ceb:Order');

        if (isset($declareConfig['OpType'])) {
            if (2 == $declareConfig['OpType']) {
                $this->sendTime = date('YmdHis', (time() + 1000));
            }
            $this->opType = $declareConfig['OpType'];
        } else {
            $this->sendTime = date('YmdHis', time());
            $this->opType   = 1;
        }

        $this->nodeLink['root_node']->appendchild($Order);

        $this->nodeLink['order'] = $Order;

        //一个订单一个订单头
        $OrderHead = $this->dom->createElement('ceb:OrderHead');
        $this->nodeLink['order']->appendchild($OrderHead);

        $OrderHeadEle = [
            'ceb:guid'      => $declareConfig['MessageId'],
            'ceb:appType'   => $this->opType,
            'ceb:appTime'   => $this->sendTime,
            'ceb:appStatus' => 2,
            'ceb:orderType' => 'I',
            'ceb:orderNo'   => $orderInfo['EntOrderNo'],

            'ceb:ebpCode' => $declareConfig['EBPEntNo'],
            'ceb:ebpName' => $declareConfig['EBPEntName'],
            'ceb:ebcCode' => $declareConfig['EBEntNo'],
            'ceb:ebcName' => $declareConfig['EBEntName'],

            'ceb:goodsValue'       => $orderInfo['OrderGoodTotal'],
            'ceb:freight'          => $orderInfo['Freight'],
            'ceb:discount'         => $orderInfo['OtherPayment'],
            'ceb:taxTotal'         => $orderInfo['Tax'],
            'ceb:acturalPaid'      => $orderInfo['ActualAmountPaid'],
            'ceb:currency'         => $this->currency,
            'ceb:buyerRegNo'       => $orderInfo['OrderDocAcount'],
            'ceb:buyerName'        => $orderInfo['OrderDocName'],
            'ceb:buyerTelephone'   => $orderInfo['OrderDocTel'],
            'ceb:buyerIdType'      => $orderInfo['OrderDocType'],
            'ceb:buyerIdNumber'    => $orderInfo['OrderDocId'],
            'ceb:payCode'          => '',
            'ceb:payName'          => '',
            'ceb:payTransactionId' => '',
            'ceb:batchNumbers'     => '',

            'ceb:consignee'          => $orderInfo['RecipientName'],
            'ceb:consigneeTelephone' => $orderInfo['RecipientTel'],
            'ceb:consigneeAddress'   => $orderInfo['RecipientAddr'],
            'ceb:note'               => '',
        ];

        //报关订单号兼容
        if (isset($orderInfo['declare_no']) && !empty($orderInfo['declare_no'])) {
            $OrderHeadEle['ceb:orderNo'] = $orderInfo['declare_no'];
        }

        //兼容订购人电话为空或者非法时,使用收货人电话的情况
        if (empty((int) $OrderHeadEle['ceb:buyerTelephone']) || (strlen($OrderHeadEle['ceb:buyerTelephone']) < 11)) {
            $OrderHeadEle['ceb:buyerTelephone'] = $OrderHeadEle['ceb:consigneeTelephone'];
        }

        $this->dom = $this->createEle($OrderHeadEle, $this->dom, $OrderHead);

        
        //商品信息
        foreach ($goodsInfo as $kk => $vv) {
            $OrderGoodsList = $this->dom->createElement('ceb:OrderList');
            $this->nodeLink['order']->appendchild($OrderGoodsList);

            $goodsListEle = [
                'ceb:gnum'         => $kk + 1,
                'ceb:itemNo'       => $vv['SKU'],
                'ceb:itemName'     => $vv['GoodsName'],
                'ceb:gmodel'       => $vv['GoodsStyle'],
                'ceb:itemDescribe' => '',
                'ceb:barCode'      => $vv['BarCode'],
                'ceb:unit'         => $vv['GUnit'],
                'ceb:qty'          => $vv['GoodsNumber'],
                'ceb:price'        => $vv['RegPrice'],
                'ceb:totalPrice'   => round($vv['RegPrice'] * $vv['GoodsNumber'], 2),
                'ceb:currency'     => $this->currency,
                'ceb:country'      => $this->currency,
                'ceb:note'         => '',
            ];

            $this->dom = $this->createEle($goodsListEle, $this->dom, $OrderGoodsList);

            $goodsListEle_arr[] = $goodsListEle;
        }

        //验证数据
        $this->checkOrderInfo($OrderHeadEle, $goodsListEle_arr);

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
    public function checkOrderInfo($order_info, $order_goods)
    {
        $this->credentialValidate->setRule([
            'ceb:orderNo' => 'require|max:60',

            'ceb:buyerRegNo' => 'require|max:60',

            'ceb:buyerName'     => 'require|max:60',
            'ceb:buyerIdType'   => 'require|max:1',
            'ceb:buyerIdNumber' => 'require|max:60',

            'ceb:buyerTelephone' => 'require|max:18',

            'ceb:consignee'          => 'require|max:100',
            'ceb:consigneeTelephone' => 'require|max:50',
            'ceb:consigneeAddress'   => 'require|max:200',
        ]);

        if (!$this->credentialValidate->check($order_info)) {
            throw new ClientError('报文订单数据: ' . $this->credentialValidate->getError());
        }

        $goods_total = 0;

        //检验商品, 并且计算总价是否相符
        foreach ($order_goods as $key => $value) {
            $goods_total += ($value['ceb:qty'] * (float) $value['ceb:price']);
        }

        if (!$this->floatCmp((string) round($goods_total, 2), (string) $order_info['ceb:goodsValue'])) {
            throw new ClientError('报文订单数据: 商品实际总值与订单记录不符。');
        }

        if (!$this->floatCmp((string) ($order_info['ceb:goodsValue'] + $order_info['ceb:freight'] + $order_info['ceb:taxTotal'] - $order_info['ceb:discount']), (string) $order_info['ceb:acturalPaid'])) {
            throw new ClientError('报文订单数据: 订单总额与实际支付金额不一致。');
        }

        return true;
    }
}
