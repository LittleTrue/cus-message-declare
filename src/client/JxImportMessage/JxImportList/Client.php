<?php

namespace customs\CustomsDeclareClient\JxImportMessage\JxImportList;

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
    public $businessType = 'PERSONAL_GOODS_DECLAR';

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
     * 进口清单申报.
     *
     * @throws ClientError
     */
    public function generateListXmlPost(array $declareConfig, array $declareParams)
    {
        $order_data = $declareParams['order_info'];
        $goods_data = $declareParams['goods_info'];

        //检查申报签名参数
        $this->checkDeclareConfig($declareConfig);
        //检查订单头参数
        $this->checkHead($order_data);

        $this->setRootNode($this->businessType);

        $goodsDeclareModuleList = $this->dom->createElement('goodsDeclareModuleList');
        $this->nodeLink['body']->appendchild($goodsDeclareModuleList);

        $goodsDeclareModule = $this->dom->createElement('goodsDeclareModule');
        $goodsDeclareModuleList->appendchild($goodsDeclareModule);

        $jkfSign = $this->dom->createElement('jkfSign');
        $goodsDeclareModule->appendchild($jkfSign);

        $jkfSignData = [
            'companyCode'  => $declareConfig['EBPEntNo'],
            'businessNo'   => date('YmdHis') . rand(1, 1000),
            'businessType' => $this->businessType,
            'declareType'  => $declareConfig['OpType'],
            'cebFlag'      => '03',
            'note'         => '',
        ];

        $this->dom = $this->createEle($jkfSignData, $this->dom, $jkfSign);

        $goodsDeclare = $this->dom->createElement('goodsDeclare');
        $goodsDeclareModule->appendchild($goodsDeclare);

        $goodsDeclareData = [
            'accountBookNo'           => '', //非必
            'ieFlag'                  => 'I',
            'preEntryNumber'          => $declareConfig['importType'] . date('YmdHis'), //4位电商编号+14位企业流水
            'importType'              => $declareConfig['importType'], //监管方式
            'inOutDateStr'            => $order_data['inOutDateStr'],
            'iePort'                  => $order_data['iePort'], //口岸代码表
            'destinationPort'         => $order_data['destinationPort'],
            'trafName'                => $order_data['trafName'], //运输工具名称，非必
            'voyageNo'                => $order_data['voyageNo'], //航班航次号，非必
            'trafNo'                  => $order_data['trafNo'], //运输工具编号，非必
            'trafMode'                => $order_data['trafMode'], //运输方式
            'declareCompanyType'      => $declareConfig['declareCompanyType'], //申报单位类别
            'declareCompanyCode'      => $declareConfig['EBEntNo'], //申报企业代码
            'declareCompanyName'      => $declareConfig['EBEntName'], //申报企业名称
            'companyName'             => $declareConfig['EBPEntName'], //电商平台名称
            'companyCode'             => $declareConfig['EBPEntNo'], //电商平台代码
            'eCommerceCode'           => $declareConfig['EBEntNo'], //电商企业代码
            'eCommerceName'           => $declareConfig['EBEntName'], //电商企业名称
            'logisCompanyName'        => $order_data['logisCompanyName'], //物流企业名称
            'logisCompanyCode'        => $order_data['logisCompanyCode'], //物流企业代码
            'orderNo'                 => $order_data['EntOrderNo'], //订单编号
            'wayBill'                 => $order_data['wayBill'], //物流运单编号
            'billNo'                  => isset($order_data['wayBill']) ? $order_data['wayBill'] : '', //提运单号，非必
            'tradeCountry'            => $order_data['tradeCountry'], //启运国（地区）
            'packNo'                  => $order_data['packNo'], //件数
            'grossWeight'             => $order_data['grossWeight'], //毛重（公斤）
            'netWeight'               => $order_data['netWeight'], //净重（公斤）
            'warpType'                => isset($order_data['warpType']) ? $order_data['warpType'] : '', //包装种类代码，非必
            'remark'                  => '',
            'declPort'                => $declareConfig['declPort'], //申报地海关代码
            'enteringPerson'          => $declareConfig['enteringPerson'], //录入人
            'enteringCompanyName'     => $declareConfig['enteringCompanyName'], //录入单位名称
            'declarantNo'             => '', //报关员代码，非必
            'customsField'            => $order_data['customsField'], //监管场所代码
            'senderName'              => $order_data['senderName'], //发件人
            'consignee'               => $order_data['consignee'], //收件人
            'senderCountry'           => $order_data['senderCountry'], //发件人国别
            'senderCity'              => isset($order_data['senderCity']) ? $order_data['senderCity'] : '', //发件人城市，非必
            'paperType'               => '1', //收件人证件类型，非必
            'paperNumber'             => '', //收件人证件号，非必
            'consigneeAddress'        => $order_data['consigneeAddress'], //收件人地址
            'purchaserTelNumber'      => $order_data['purchaserTelNumber'], //购买人电话
            'buyerIdType'             => $order_data['buyerIdType'], //订购人证件类型
            'buyerIdNumber'           => $order_data['buyerIdNumber'], //订购人证件号码
            'buyerName'               => $order_data['buyerName'], //订购人姓名
            'worth'                   => $order_data['worth'], //价值
            'feeAmount'               => $order_data['feeAmount'], //运费
            'insureAmount'            => $order_data['insureAmount'], //保费
            'currCode'                => $order_data['currency'], //币制
            'mainGName'               => $order_data['mainGName'], //主要货物名称
            'internalAreaCompanyNo'   => '', //区内企业代码，非必
            'internalAreaCompanyName' => '', //区内企业名称，非必
            'assureCode'              => $declareConfig['assureCode'], //担保企业编号
            'applicationFormNo'       => '', //申请单编号，非必
            'isAuthorize'             => '1', //是否授权
            'licenseNo'               => '', //许可证号，非必
        ];

        $this->createEle($goodsDeclareData, $this->dom, $goodsDeclareModule);

        $goodsDeclareDetails = $this->dom->createElement('goodsDeclareDetails');
        $goodsDeclareModule->appendchild($goodsDeclareDetails);

        foreach ($goods_data as $kk => $vv) {
            $goodsDeclareDetail = $this->dom->createElement('goodsDeclareDetail');
            $goodsDeclareDetails->appendchild($goodsDeclareDetail);

            $goodsListEle = [
                'goodsOrder'       => $kk + 1,
                'codeTs'           => $vv['SKU'],
                'goodsItemNo'      => $vv['goodsItemNo'], //企业商品货号,金二账册必填
                'itemRecordNo'     => $vv['itemRecordNo'], //账册备案料号,保税必填
                'itemName'         => '', //企业商品品名,非必
                'goodsName'        => $vv['GoodsName'], //商品名称
                'goodsModel'       => $vv['GoodsStyle'], //商品规格型号
                'originCountry'    => $vv['OriginCountry'], //原产国（地区）
                'tradeCurr'        => $vv['currency'], //币制
                'tradeTotal'       => '', //成交总价，非必
                'declPrice'        => $vv['RegPrice'], //单价
                'declTotalPrice'   => $vv['declTotalPrice'], //总价,申报数量乘以申报单价
                'useTo'            => '', //用途，非必
                'declareCount'     => $vv['GoodsNumber'], //数量
                'goodsUnit'        => $vv['GUnit'], //计量单位
                'goodsGrossWeight' => '', //商品毛重，非必
                'firstUnit'        => $vv['firstUnit'], //法定计量单位
                'firstCount'       => $vv['firstCount'], //法定数量
                'secondUnit'       => '', //第二计量单位，非必
                'secondCount'      => '', //第二数量，非必
                'productRecordNo'  => '', //产品国检备案编号，非必
                'webSite'          => '', //商品网址，非必
                'barCode'          => '', //条形码，非必
                'note'             => '',
                'tradeCountry'     => $vv['tradeCountry'], //贸易国
            ];
            $this->dom = $this->createEle($goodsListEle, $this->dom, $goodsDeclareDetail);
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
        $this->credentialValidate->setRule(
            [
                'businessNo' => 'require|max:20',
                'OpType'     => 'require|max:1',
                'EBPEntNo'   => 'require|max:20', //电商平台在跨境电商综合服务平台的备案名称
                'EBPEntName' => 'require|max:200', //电商平台在跨境电商综合服务的备案编号
                'EBEntNo'    => 'require|max:60', //电商企业编码
                'EBEntName'  => 'require|max:200', //电商企业名称
            ]
        );

        if (!$this->credentialValidate->check($declareConfig)) {
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
        $this->credentialValidate->setRule(
            [
                'preEntryNumber'          => 'require|max:18',
                'importType'              => 'require|max:1',
                'inOutDateStr'            => 'require',
                'iePort'                  => 'require|max:5',
                'destinationPort'         => 'require|max:5',
                'trafName '               => 'max:100',
                'voyageNo '               => 'max:32',
                'trafNo'                  => 'max:100',
                'trafMode '               => 'require|max:30',
                'declareCompanyType'      => 'require|max:30',
                'declareCompanyCode'      => 'require|max:20',
                'declareCompanyName'      => 'require|max:200',
                'companyName'             => 'require|max:200',
                'companyCode'             => 'require|max:20',
                'eCommerceCode'           => 'require|max:20',
                'eCommerceName'           => 'require|max:200',
                'logisCompanyName'        => 'require|max:200',
                'logisCompanyCode'        => 'require|max:20',
                'orderNo'                 => 'require|max:50',
                'wayBill'                 => 'require|max:50',
                'billNo'                  => 'max:37',
                'tradeCountry '           => 'require|max:20',
                'packNo'                  => 'require|number',
                'grossWeight'             => 'require|number',
                'netWeight'               => 'require|number',
                'warpType'                => 'max:20',
                'remark'                  => 'max:200',
                'declPort'                => 'require|max:5',
                'enteringPerson'          => 'require|max:20',
                'enteringCompanyName'     => 'require|max:30',
                'declarantNo'             => 'max:20',
                'customsField '           => 'require|max:20',
                'senderName'              => 'require|max:20',
                ' consignee'              => 'require|max:20',
                'senderCountry'           => 'require|max:20',
                'senderCity '             => 'max:20',
                'paperType '              => 'max:1',
                'paperNumber'             => 'max:50',
                'consigneeAddress'        => 'require|max:255',
                'purchaserTelNumber'      => 'require|max:30',
                'buyerIdType'             => 'require|max:1',
                'buyerIdNumber'           => 'require|max:60',
                'buyerName'               => 'require|max:60',
                'worth'                   => 'require|number',
                'feeAmount'               => 'require|number',
                'insureAmount'            => 'require|number',
                'currCode'                => 'require|max:18',
                'mainGName'               => 'require|max:255',
                'internalAreaCompanyNo'   => 'max:50',
                'internalAreaCompanyName' => 'max:200',
                'assureCode'              => 'require|max:50',
                'applicationFormNo'       => 'max:30',
                'isAuthorize'             => 'require|max:1',
            ]
        );

        if (!$this->credentialValidate->check($head)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }
    }
}
