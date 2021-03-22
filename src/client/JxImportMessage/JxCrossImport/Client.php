<?php
namespace customs\CustomsDeclareClient\JxImportMessage\JxCrossImport;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\JxImportMessageBuild;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * 客户端
 */
class Client extends BaseClient
{
    use JxImportMessageBuild;

    //报文业务类型
    public $businessType = 'IMPORTORDER';

    /**
     * @var string aesKey aes私钥(经过base64转码的)
     */
    public $aesKey;

    /**
     * @var string privateKey rsa私钥(经过base64转码的)
     */
    public $privateKey;

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
     * 进口订单申报
     * 
     * @throws ClientError
     */
    public function generateOrderXmlPost(array $declareConfig, array $declareParams)
    {
        $order_data = $declareParams['order_info'];
        $goods_data = $declareParams['goods_info'];

        //检查申报签名参数
        $this->checkDeclareConfig($declareConfig);
        //检查订单头参数
        $this->checkHead($order_data);
        //检查购买者参数
        $this->checkPurchaser($order_data);

        $this->aesKey = $declareConfig['aesKey'];
        $this->privateKey = $declareConfig['privateKey'];
        
        $this->setRootNode($this->businessType);
        
        $orderInfoList = $this->dom->createElement('orderInfoList');
        $this->nodeLink['body']->appendchild($orderInfoList);

        $orderInfo = $this->dom->createElement('orderInfo');
        $orderInfoList->appendchild($orderInfo);

        $jkfSign = $this->dom->createElement('jkfSign');
        $orderInfo->appendchild($jkfSign);

        $jkfSignData = [
            'companyCode' => $declareConfig['EBPEntNo'],
            'businessNo' => date('YmdHis') . rand(1, 1000),
            'businessType' => $this->businessType,
            'declareType' => $declareConfig['OpType'],
            'cebFlag' => '03',
            'note' => '',
        ];

        $this->dom = $this->createEle($jkfSignData, $this->dom, $jkfSign);

        $jkfOrderImportHead = $this->dom->createElement('jkfOrderImportHead');
        $orderInfo->appendchild($jkfOrderImportHead);
  
        $jkfOrderImportHeadData = [
            'companyCode' => $declareConfig['EBPEntNo'],
            'companyName' => $declareConfig['EBPEntName'],
            'eCommerceCode' => $declareConfig['EBEntNo'], //电商企业编号
            'eCommerceName' => $declareConfig['EBEntName'], //电商企业名称
            'ieFlag' => 'I',
            'payType' => $order_data['payType'], //01:银行卡支付 02:余额支付 03:其他
            'payCompanyCode' => $order_data['payCompanyCode'],
            'payCompanyName' => $order_data['payCompanyName'],
            'payNumber' => $order_data['payNumber'],
            'orderTotalAmount' => $order_data['orderTotalAmount'],
            'orderGoodsAmount' => $order_data['OrderGoodTotal'],
            'discount' => $order_data['OtherPayment'],
            'orderNo' => $order_data['EntOrderNo'],
            'orderTaxAmount' => $order_data['Tax'],
            'feeAmount' => isset($order_data['Freight']) ? $order_data['Freight'] : 0,
            'insureAmount' => $order_data['insureAmount'],
            'tradeTime' => date('Y-m-d H:i:s', $order_data['tradeTime']),
            'currCode' => $order_data['currency'],
            'totalAmount' => $order_data['ActualAmountPaid'],
            'consigneeEmail' => '',//非必
            'consigneeTel' => $order_data['RecipientTel'],
            'consignee' => $order_data['RecipientName'],
            'consigneeAddress' => $order_data['RecipientAddr'],
            'totalCount' => $order_data['totalCount'],
            'batchNumbers' => '',//非必
            'consigneeDitrict' => '',//非必
            'postMode' => '',//非必
            'senderCountry' => $order_data['senderCountry'],//发件人国别
            'senderName' => $order_data['senderName'],//发件人姓名
            'purchaserId' => $order_data['OrderDocAcount'],//购买人ID
            'logisCompanyName' => $order_data['logisCompanyName'],//物流企业名称
            'logisCompanyCode' => $order_data['logisCompanyCode'],//物流企业编号
            'zipCode' => '',//非必
            'note' => '',//非必
            'wayBills' => '',//非必
            'rate' => '',//非必
            'userProcotol' => '本人承诺所购买商品系个人合理自用，现委托商家代理申报、代缴税款等通关事宜，本人保证遵守《海关法》和国家相关法律法规，保证所提供的身份信息和收货信息真实完整，无侵犯他人权益的行为，以上委托关系系如实填写，本人愿意接受海关、检验检疫机构及其他监管部门的监管，并承担相应法律责任。',
        ];

        $this->createEle($jkfOrderImportHeadData, $this->dom, $jkfOrderImportHead);

        $jkfOrderDetailList = $this->dom->createElement('jkfOrderDetailList');
        $orderInfo->appendchild($jkfOrderDetailList);

        foreach ($goods_data as $kk => $vv) {
            $jkfOrderDetail = $this->dom->createElement('jkfOrderDetail');
            $jkfOrderDetailList->appendchild($jkfOrderDetail);

            $goodsListEle = [
                'goodsOrder' => $kk + 1,
                'goodsName' => $vv['GoodsName'],
                'codeTs' => $vv['SKU'],
                'goodsModel' => $vv['GoodsStyle'],
                'originCountry' => $vv['OriginCountry'],
                'unitPrice' => $vv['RegPrice'],
                'currency' => $vv['currency'],
                'goodsCount' => $vv['GoodsNumber'],
                'goodsUnit' => $vv['GUnit'],
                'grossWeight' => '',//非必
                'barCode' => $vv['BarCode'],//非必
                'note' => '',//非必
            ];
            $this->dom = $this->createEle($goodsListEle, $this->dom, $jkfOrderDetail);
        }

        $jkfGoodsPurchaser = $this->dom->createElement('jkfGoodsPurchaser');
        $orderInfo->appendchild($jkfGoodsPurchaser);

        $jkfGoodsPurchaserData = [
            'id' => $order_data['OrderDocAcount'],
            'name' => $order_data['OrderDocName'],
            'email' => '',//非必
            'telNumber' => $order_data['OrderDocTel'],
            'paperType' => '01',
            'paperNumber' => $order_data['OrderDocId'],
            'address' => '',//非必
        ];
        $this->dom = $this->createEle($jkfGoodsPurchaserData, $this->dom, $jkfGoodsPurchaser);

        // $this->dom->formatOutput = true;
        $xml_string = $this->dom->saveXML();     

        return [
            'content'=>$this->aesEncrypt($xml_string),
            'msgType'=>$this->businessType,
            'dataDigest'=>$this->rsaSign($xml_string),
            'sendCode' => $declareConfig['EBPEntNo'],
        ];
    }

    /**
     * 检查申报前置参数
     * 
     * @throws ClientError
     */
    private function checkDeclareConfig($declareConfig)
    {
        $this->credentialValidate->setRule(
            [
                'businessNo' => 'require|max:20',
                'OpType' => 'require|max:1',
                'EBPEntNo' => 'require|max:20', //电商平台在跨境电商综合服务平台的备案名称
                'EBPEntName' => 'require|max:200', //电商平台在跨境电商综合服务的备案编号
                'EBEntNo' => 'require|max:60',//电商企业编码
                'EBEntName' => 'require|max:200',//电商企业名称
                'aesKey' => 'require',//AES密钥
                'privateKey' => 'require',//RSA私钥
            ]
        );

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        } 
    }

    /**
     * 检查申报订单头
     * 
     * @throws ClientError
     */
    private function checkHead($head)
    {
        $this->credentialValidate->setRule(
            [
                'payType' => 'require|max:2',
                'payCompanyCode' => 'require|max:50',
                'payCompanyName' => 'require|max:50',
                'payNumber' => 'require|max:60',
                'ActualAmountPaid' => 'require',
                'EntOrderNo' => 'require|max:60',
                'Tax' =>'require',
                'OrderGoodTotal' =>'require',
                // 'feeAmount' => '', //非必
                'insureAmount' => 'require',
                'tradeTime' => 'require|max:25',
                'currency' =>'require|max:3',
                // 'totalAmount' => '',
                // 'consigneeEmail' => '',
                'RecipientTel' => 'require|max:60',
                'RecipientName' => 'require|max:60',
                'RecipientAddr' => 'require|max:255',
                'totalCount' => 'require',
                // 'postMode' => '',
                'senderCountry' => 'require|max:3',
                'senderName' => 'require|max:200',
                'OrderDocAcount' => 'require|max:100',
                'logisCompanyName' => 'require|max:200',
                'logisCompanyCode' => 'require|max:20',
                // 'zipCode' => '',
                // 'wayBills' =>'',
                // 'rate' =>'',
                'OtherPayment' => 'require',
                // 'batchNumbers' => '',
                // 'consigneeDitrict' => '',
            ]
        );

        if (!$this->credentialValidate->check($head)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }
    }

    /**
     * 检查申报购买人信息
     * 
     * @throws ClientError
     */
    private function checkPurchaser($purchaser)
    {
        $this->credentialValidate->setRule(
            [
                'OrderDocAcount' => 'require|max:100',
                'OrderDocName' => 'require|max:100' ,
                // 'email' => '',
                'OrderDocTel' => 'require|max:30',
                // 'address' =>'',
                'OrderDocId' => 'require|max:100',
            ]
        );

        if (!$this->credentialValidate->check($purchaser)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }
    }

    /**
     * 报文aes加密
     */
    private function aesEncrypt($string)
    {
        return openssl_encrypt($string, 'AES-128-ECB', base64_decode($this->aesKey));
    }

    /**
     * 报文数字签名
     */
    private function rsaSign($string)
    {
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($this->privateKey, 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
        $key = openssl_get_privatekey($privateKey);
        openssl_sign($string, $dataDigest, $key);
        
        return base64_encode($dataDigest);
    }



}