<?php

namespace customs\CustomsDeclareClient\CebMessage\ChecklistCrossImport;

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
    public $messageType = 'CEB621Message';

    /**
     * @var Application
     */
    protected $credentialValidate;

    //操作类型
    private $opType;

    //报文发送时间
    private $sendTime;

    //报文发送日期
    private $sendDay;

    //进出口类型
    private $ieFlag = 'I';

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
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        $rule = [
            //修改
            'PreNo'      => 'max:50',
            'CusEListNo' => 'max:18',

            'EBPEntNo'   => 'require|max:50',
            'EBPEntName' => 'require|max:100',

            'EBEntNo'   => 'require|max:18',
            'EBEntName' => 'require|max:100',

            'EHSEntNo'   => 'require|max:18',
            'EHSEntName' => 'require|max:100',

            'DanBaoEntNo' => 'require|max:30',

            'CustomsCode' => 'require|max:4',
            'IEPort'      => 'require|max:4',

            'TradeMode' => 'require|max:4',
            'SvPCode'   => 'require|max:10',

            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',

            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',
        ];

        //根据贸易模型选择配置
        if (isset($declareConfig['TradeMode'])) {
            if ('9610' == $declareConfig['TradeMode']) {
                array_merge($rule, [
                    //9610
                    'TrafNo'   => 'require|max:100',
                    'VoyageNo' => 'require|max:37',
                    'BillNo'   => 'require|max:37',
                ]);

                $declareConfig['EmsNo']    = '';
                $declareConfig['AreaCode'] = '';
                $declareConfig['AreaName'] = '';
            } elseif ('1210' == $declareConfig['TradeMode']) {
                array_merge($rule, [
                    //1210
                    'EmsNo'    => 'require|max:30',
                    'AreaCode' => 'require|max:18',
                    'AreaName' => 'require|max:100',
                ]);

                $declareConfig['TrafNo']   = '';
                $declareConfig['AirNumber'] = '';
                $declareConfig['BillNo']   = '';
            }
        } else {
            throw new ClientError('报文传输配置, 贸易模式未设置。');
        }

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        //根节点生成--父类
        $this->setRootNode($declareConfig['MessageId']);

        $checklistInfo = $declareParams['checklist_info'];
        $goodsInfo     = $declareParams['goods_info'];

        //一个报文只申报一张订单
        $Inventory = $this->dom->createElement('ceb:Inventory');
        $this->nodeLink['root_node']->appendchild($Inventory);
        $this->nodeLink['Inventory'] = $Inventory;

        //一个清单一个清单头
        $InventoryHead = $this->dom->createElement('ceb:InventoryHead');
        $this->nodeLink['Inventory']->appendchild($InventoryHead);

        if (isset($declareConfig['OpType'])) {
            if (2 == $declareConfig['OpType']) {
                $this->sendTime = date('YmdHis', (time() + 1000));
                //修改时部分字段必填
                if (empty($declareConfig['CusEListNo']) || empty($declareConfig['invtNo'])) {
                    throw new ClientError('修改报文, 海关单据号invtNo和预录入号preNo信息必填。');
                }
            } else {
                $this->sendTime = date('YmdHis', time());
            }
            $this->opType = $declareConfig['OpType'];
        } else {
            $this->sendTime = date('YmdHis', time());

            $this->opType = 1;
        }

        $this->sendDay = date('Ymd');

        $InventoryHeadEle = [
            'ceb:guid'      => $declareConfig['MessageId'],
            'ceb:appType'   => $this->opType,
            'ceb:appTime'   => $this->sendTime,
            'ceb:appStatus' => 2,

            'ceb:orderNo' => $checklistInfo['EntOrderNo'],

            'ceb:ebpCode' => $declareConfig['EBPEntNo'],
            'ceb:ebpName' => $declareConfig['EBPEntName'],
            'ceb:ebcCode' => $declareConfig['EBEntNo'],
            'ceb:ebcName' => $declareConfig['EBEntName'],

            'ceb:logisticsNo'   => $checklistInfo['EntWaybillNo'],
            'ceb:logisticsCode' => $declareConfig['EHSEntNo'],
            'ceb:logisticsName' => $declareConfig['EHSEntName'],

            //清单编号
            'ceb:copNo' => $checklistInfo['EntEListNo'],
            'ceb:preNo' => 2 == $this->opType ? $declareConfig['preNo'] : '',

            //1210
            'ceb:emsNo' => $declareConfig['EmsNo'],

            'ceb:assureCode' => $declareConfig['DanBaoEntNo'],  //担保企业在KJ报文的是电商企业

            'ceb:invtNo' => 2 == $this->opType ? $declareConfig['CusEListNo'] : '',

            'ceb:ieFlag'           => $this->ieFlag,
            'ceb:declTime'         => $this->sendDay,
            'ceb:customsCode'      => $declareConfig['CustomsCode'],
            'ceb:portCode'         => $declareConfig['IEPort'],
            'ceb:ieDate'           => $this->sendDay,
            'ceb:buyerIdType'      => 1,  //限定为身份证
            'ceb:buyerIdNumber'    => $checklistInfo['OrderDocId'],
            'ceb:buyerName'        => $checklistInfo['OrderDocName'],
            'ceb:buyerTelephone'   => $checklistInfo['OrderDocTel'],
            'ceb:consigneeAddress' => $checklistInfo['RecipientAddr'],

            'ceb:agentCode' => $declareConfig['DeclEntNo'],
            'ceb:agentName' => $declareConfig['DeclEntName'],

            //1210
            'ceb:areaCode' => $declareConfig['AreaCode'],
            'ceb:areaName' => $declareConfig['AreaName'],

            'ceb:tradeMode' => $declareConfig['TradeMode'],

            //运输方式
            'ceb:trafMode' => $checklistInfo['trafCode'],
            //9610
            'ceb:trafNo'   => $declareConfig['TrafNo'],
            'ceb:voyageNo' => $declareConfig['AirNumber'],
            'ceb:billNo'   => $declareConfig['BillNo'],

            'ceb:loctNo'    => $declareConfig['SvPCode'],
            'ceb:licenseNo' => '',

            'ceb:country'     => $checklistInfo['ShipperCountryCode'],
            'ceb:freight'     => $checklistInfo['FeeRate'],
            'ceb:insuredFee'  => $checklistInfo['InsurRate'],
            'ceb:currency'    => $this->currency,
            'ceb:wrapType'    => $checklistInfo['WrapType'],
            'ceb:packNo'      => 1,
            'ceb:grossWeight' => $checklistInfo['TotalGrossWeight'], //需要与订单申报中一致性相关
            'ceb:netWeight'   => $checklistInfo['TotalNetWeight'], //需要与订单申报中一致性相关
            'ceb:note'        => '',
        ];

        if (empty($InventoryHeadEle['ceb:invtNo'])) {
            unset($InventoryHeadEle['ceb:invtNo']);
        }

        //兼容订购人电话为空或者非法时,使用收货人电话的情况
        if (empty((int) $InventoryHeadEle['ceb:buyerTelephone']) || (strlen($InventoryHeadEle['ceb:buyerTelephone']) < 11)) {
            $InventoryHeadEle['ceb:buyerTelephone'] = $checklistInfo['RecipientTel'];
        }

        $this->dom = $this->createEle($InventoryHeadEle, $this->dom, $InventoryHead);

        //商品信息
        foreach ($goodsInfo as $kk => $vv) {
            $InventoryList = $this->dom->createElement('ceb:InventoryList');
            $this->nodeLink['Inventory']->appendchild($InventoryList);

            $InventoryListEle = [
                'ceb:gnum'         => $kk + 1,
                //保税进口账册备案
                'ceb:itemRecordNo' => $vv['EmsNo'],
                'ceb:itemNo'       => $vv['SKU'],
                'ceb:itemName'     => $vv['GoodsName'],
                'ceb:gcode'        => $vv['HSCode'],

                'ceb:gname'  => $vv['GoodsName'],
                'ceb:gmodel' => $vv['GoodsStyle'], //待补充

                'ceb:barCode'  => $vv['BarCode'],
                'ceb:country'  => $vv['OriginCountry'],
                'ceb:currency' => $this->currency,
                'ceb:qty'      => $vv['GoodsNumber'],
                'ceb:unit'     => $vv['GUnit'], //成交计量单位
                'ceb:qty1'     => $vv['UnitSum1'] * $vv['GoodsNumber'],
                'ceb:unit1'    => $vv['StdUnit'],

                'ceb:qty2'  => empty($vv['UnitSum2'] * $vv['GoodsNumber']) ? '' : ($vv['UnitSum2'] * $vv['GoodsNumber']),
                'ceb:unit2' => empty($vv['SecUnit']) ? '' : $vv['SecUnit'],

                'ceb:price'      => (float) $vv['GoodsPrice'],
                'ceb:totalPrice' => round((float) $vv['GoodsPrice'] * $vv['GoodsNumber'], 2),
                'ceb:note'       => '',
            ];

            if (empty($InventoryListEle['ceb:qty2']) || empty($InventoryListEle['ceb:unit2'])) {
                unset($InventoryListEle['ceb:qty2'], $InventoryListEle['ceb:unit2']);
            }

            $this->dom = $this->createEle($InventoryListEle, $this->dom, $InventoryList);

            $goodsListEle_arr[] = $InventoryListEle;
        }

        //验证数据
        $this->checkInfo($InventoryHeadEle, $goodsListEle_arr);

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
     * 定义验证器来校验清单和清单商品信息.
     */
    public function checkInfo($checklistInfo)
    {
        //根据不同的贸易模式, 区分验证规则
        $rules = [
            'ceb:orderNo' => 'require|max:60',

            //清单编号
            'ceb:copNo' => 'require|max:20',

            'ceb:buyerIdNumber'  => 'require|max:20',
            'ceb:buyerName'      => 'require|max:60',
            'ceb:buyerTelephone' => 'require|max:30',

            'ceb:consigneeAddress' => 'require|max:200',

            //运输方式
            'ceb:trafMode' => 'require|max:4',

            'ceb:country'     => 'require|max:4',
            'ceb:freight'     => 'require|number',
            'ceb:insuredFee'  => 'require|number',
            'ceb:wrapType'    => 'require|max:4',
            'ceb:grossWeight' => 'require|number',
            'ceb:netWeight'   => 'require|number',
        ];

        $this->credentialValidate->setRule($rules);

        if (!$this->credentialValidate->check($checklistInfo)) {
            throw new ClientError('报文清单数据: ' . $this->credentialValidate->getError());
        }

        return true;
    }
}
