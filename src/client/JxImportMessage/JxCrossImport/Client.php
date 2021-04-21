<?php

namespace customs\CustomsDeclareClient\JxImportMessage\JxCrossImport;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;
use customs\CustomsDeclareClient\Base\JxImportMessageBuild;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    use JxImportMessageBuild;

    //报文业务类型
    public $businessType = 'IMPORTORDER';

    /**
     * @var Application
     */
    protected $credentialValidate;

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
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        //检查申报签名参数
        $this->checkDeclareConfig($declareConfig);

        $this->setRootNode($this->businessType);

        $orderInfoList = $this->dom->createElement('orderInfoList');
        $this->nodeLink['body']->appendchild($orderInfoList);

        foreach ($declareParams as $key => $declareItem) {
            $order_data = $declareItem['order_info'];
            $goods_data = $declareItem['goods_info'];

            $orderInfo = $this->dom->createElement('orderInfo');
            $orderInfoList->appendchild($orderInfo);

            $jkfSign = $this->dom->createElement('jkfSign');
            $orderInfo->appendchild($jkfSign);

            $jkfSignData = [
                'companyCode'  => $declareConfig['companyCode'],
                'businessNo'   => $order_data['businessNo'],
                'businessType' => $this->businessType,
                'declareType'  => $declareConfig['declareType'],
                'cebFlag'      => '03',
                'note'         => '',
            ];

            $this->dom = $this->createEle($jkfSignData, $this->dom, $jkfSign);

            $jkfOrderImportHead = $this->dom->createElement('jkfOrderImportHead');
            $orderInfo->appendchild($jkfOrderImportHead);

            $jkfOrderImportHeadData = [
                'companyCode'      => $declareConfig['companyCode'],
                'companyName'      => $declareConfig['companyName'],
                'eCommerceCode'    => $declareConfig['eCommerceCode'], //电商企业编号
                'eCommerceName'    => $declareConfig['eCommerceName'], //电商企业名称
                'ieFlag'           => 'I',
                'payType'          => $order_data['payType'], //01:银行卡支付 02:余额支付 03:其他
                'payCompanyCode'   => $order_data['payCompanyCode'],
                'payCompanyName'   => $order_data['payCompanyName'],
                'payNumber'        => $order_data['payNumber'],
                'orderTotalAmount' => $order_data['orderTotalAmount'],
                'orderGoodsAmount' => $order_data['orderGoodsAmount'],
                'discount'         => $order_data['discount'],
                'orderNo'          => $order_data['orderNo'],
                'orderTaxAmount'   => $order_data['orderTaxAmount'],
                'feeAmount'        => isset($order_data['feeAmount']) ? $order_data['feeAmount'] : 0,
                'insureAmount'     => $order_data['insureAmount'],
                'tradeTime'        => date('Y-m-d H:i:s', $order_data['tradeTime']),
                'currCode'         => $order_data['currCode'],
                'totalAmount'      => $order_data['totalAmount'],
                //收件人信息
                'consigneeEmail'   => isset($order_data['consigneeEmail']) ? $order_data['consigneeEmail'] : '', //非必
                'consigneeTel'     => $order_data['consigneeTel'],
                'consignee'        => $order_data['consignee'],
                'consigneeAddress' => $order_data['consigneeAddress'],
                'consigneeDitrict' => isset($order_data['consigneeDitrict']) ? $order_data['consigneeDitrict'] : '', //非必
                'totalCount'       => $order_data['totalCount'],
                'batchNumbers'     => isset($order_data['batchNumbers']) ? $order_data['batchNumbers'] : '', //非必
                'postMode'         => isset($order_data['postMode']) ? $order_data['postMode'] : '', //非必
                'senderCountry'    => $order_data['senderCountry'], //发件人国别
                'senderName'       => $order_data['senderName'], //发件人姓名
                'purchaserId'      => $order_data['purchaserId'], //购买人ID
                'logisCompanyName' => $order_data['logisCompanyName'], //物流企业名称
                'logisCompanyCode' => $order_data['logisCompanyCode'], //物流企业编号
                'zipCode'          => '', //非必
                'note'             => '', //非必
                'wayBills'         => '', //非必
                'rate'             => '', //非必
                'userProcotol'     => '本人承诺所购买商品系个人合理自用，现委托商家代理申报、代缴税款等通关事宜，本人保证遵守《海关法》和国家相关法律法规，保证所提供的身份信息和收货信息真实完整，无侵犯他人权益的行为，以上委托关系系如实填写，本人愿意接受海关、检验检疫机构及其他监管部门的监管，并承担相应法律责任。',
            ];

            $this->createEle($jkfOrderImportHeadData, $this->dom, $jkfOrderImportHead);

            $jkfOrderDetailList = $this->dom->createElement('jkfOrderDetailList');
            $orderInfo->appendchild($jkfOrderDetailList);

            foreach ($goods_data as $kk => $vv) {
                $jkfOrderDetail = $this->dom->createElement('jkfOrderDetail');
                $jkfOrderDetailList->appendchild($jkfOrderDetail);

                $goodsListEle = [
                    'goodsOrder'    => $kk + 1,
                    'goodsName'     => $vv['goodsName'],
                    'codeTs'        => $vv['codeTs'],
                    'goodsModel'    => $vv['goodsModel'],
                    'originCountry' => $vv['originCountry'],
                    'unitPrice'     => $vv['unitPrice'],
                    'currency'      => $vv['currency'],
                    'goodsCount'    => $vv['goodsCount'],
                    'goodsUnit'     => $vv['goodsUnit'],
                    'grossWeight'   => isset($vv['grossWeight']) ? $vv['grossWeight'] : '', //非必
                    'barCode'       => isset($vv['barCode']) ? $vv['barCode'] : '', //非必
                    'note'          => '',
                ];
                $this->dom = $this->createEle($goodsListEle, $this->dom, $jkfOrderDetail);
            }

            $jkfGoodsPurchaser = $this->dom->createElement('jkfGoodsPurchaser');
            $orderInfo->appendchild($jkfGoodsPurchaser);

            $jkfGoodsPurchaserData = [
                'id'          => $order_data['purchaserId'],
                'name'        => $order_data['name'],
                'email'       => $order_data['email'], //非必
                'telNumber'   => $order_data['telNumber'],
                'paperType'   => '01',
                'paperNumber' => $order_data['paperNumber'],
                'address'     => $order_data['address'], //非必
            ];

            //检查订单头参数
            $this->checkHead($jkfOrderImportHeadData);
            //检查购买者参数
            $this->checkPurchaser($jkfGoodsPurchaserData);

            $this->dom = $this->createEle($jkfGoodsPurchaserData, $this->dom, $jkfGoodsPurchaser);
        }

        return trim($this->dom->saveXML(), '');
    }

    /**
     * 检查申报前置参数.
     *
     * @throws ClientError
     */
    private function checkDeclareConfig($declareConfig)
    {
        $rules = [
            'declareType'   => 'require|max:1',
            'companyCode'   => 'require|max:20', //电商平台在跨境电商综合服务平台的备案名称
            'companyName'   => 'require|max:200', //电商平台在跨境电商综合服务的备案编号
            'eCommerceCode'  => 'require|max:60', //电商企业编码
            'eCommerceName' => 'require|max:200', //电商企业名称
        ];

        if (!$this->credentialValidate->check($declareConfig, $rules)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }
    }

    /**
     * 检查申报订单头.
     *
     * @throws ClientError
     */
    private function checkHead($head)
    {
        $rules = [
            'payType'          => 'require|max:2',
            'payCompanyCode'   => 'require|max:50',
            'payCompanyName'   => 'max:50',
            'payNumber'        => 'require|max:60',
            'orderNo'          => 'require|max:60',
            'orderGoodsAmount' => 'require|float',
            'orderTotalAmount' => 'require|float',
            'orderTaxAmount'   => 'require|float',
            'insureAmount'     => 'require|float',
            'tradeTime'        => 'require|max:25',
            'currCode'         => 'require|max:3',
            'consigneeTel'     => 'require|max:60',
            'consignee'        => 'require|max:60',
            'consigneeAddress' => 'require|max:255',
            'totalCount'       => 'require|float',
            'senderCountry'    => 'require|max:3',
            'senderName'       => 'require|max:200',
            'purchaserId'      => 'require|max:100',
            'logisCompanyName' => 'require|max:200',
            'logisCompanyCode' => 'require|max:20',
            'discount'         => 'require|float',
        ];

        if (!$this->credentialValidate->check($head, $rules)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }
    }

    /**
     * 检查申报购买人信息.
     *
     * @throws ClientError
     */
    private function checkPurchaser($purchaser)
    {
        $rules = [
            'id'          => 'require|max:100',
            'name'        => 'require|max:100',
            'telNumber'   => 'require|max:30',
            'paperNumber' => 'require|max:100',
        ];

        if (!$this->credentialValidate->check($purchaser, $rules)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }
    }
}
