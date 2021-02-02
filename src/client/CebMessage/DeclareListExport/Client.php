<?php

namespace customs\CustomsDeclareClient\CebMessage\DeclareListExport;

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
    public $messageType = 'CEB603Message';

    /**
     * @var Application
     */
    protected $credentialValidate;

    //报文发送时间
    private $sendTime;

    //报文发送日期
    private $sendDay;

    //操作类型
    private $opType;

    //进出口标志
    private $ieFlag = 'E';

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->credentialValidate = $app['credential'];
    }

    /**
     * 出口申报清单.
     */
    public function generateXmlPost(array $declareConfig, array $declareParams)
    {
        $rule = [
            //修改
            // 'EBPEntNo'   => 'require|max:50',
            // 'EBPEntName' => 'require|max:100',

            // 'EBEntNo'   => 'require|max:18',
            // 'EBEntName' => 'require|max:100',

            // 'EHSEntNo'   => 'require|max:18',
            // 'EHSEntName' => 'require|max:100',
            // 'EntWaybillNo' => 'require|max:80',

            // 'ceb:logisticsNo'   => $checklistInfo['EntWaybillNo'],
            // 'ceb:logisticsCode' => $declareConfig['EHSEntNo'],
            // 'ceb:logisticsName' => $declareConfig['EHSEntName'],

            // 'CustomsCode' => 'require|max:4',

            // 'TradeMode' => 'require|max:4',

            'DeclEntNo'    => 'require|max:18',
            'DeclEntName'  => 'require|max:100',
            'DeclEntDxpid' => 'require|max:30',

            'MessageId' => 'require|max:36',

            'OpType' => 'require|max:1',
        ];

        // //根据贸易模型选择配置
        // if (isset($declareConfig['TradeMode'])) {
        //     if ('9610' == $declareConfig['TradeMode']) {
        //         array_merge($rule, [
        //             //9610
        //             'iacCode' => 'require|max:18',
        //             'iacName' => 'require|max:100',
        //             'emsNo'   => 'require|max:30',
        //         ]);
        //     }
        // } else {
        //     throw new ClientError('报文传输配置, 贸易模式未设置。');
        // }

        $this->credentialValidate->setRule($rule);

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        $this->sendDay  = date('Ymd');
        $this->sendTime = date('YmdHis', time());
        $this->opType   = $declareConfig['OpType'];

        //根节点生成--父类
        $this->setRootNode($declareConfig['MessageId']);

        foreach ($declareParams as $key => $value) {
            $head = $value['head'];

            $list = $value['list'];

            //一个报文有多个申报订单
            $Inventory = $this->dom->createElement('ceb:Inventory');
            $this->nodeLink['root_node']->appendchild($Inventory);
            $this->nodeLink['Inventory'] = $Inventory;

            //一个清单一个清单头
            $InventoryHead = $this->dom->createElement('ceb:InventoryHead');
            $this->nodeLink['Inventory']->appendchild($InventoryHead);

            $InventoryHeadEle = [
                'ceb:guid'      => $declareConfig['MessageId'],
                'ceb:appType'   => $declareConfig['OpType'],
                'ceb:appTime'   => $this->sendTime,
                'ceb:appStatus' => $declareConfig['appStatus'],

                'ceb:customsCode' => $head['CustomsCode'],

                'ceb:ebpCode' => $head['EBPEntNo'],
                'ceb:ebpName' => $head['EBPEntName'],

                'ceb:orderNo' => $head['orderNo'],

                'ceb:logisticsCode' => $head['EHSEntNo'],
                'ceb:logisticsName' => $head['EHSEntName'],
                'ceb:logisticsNo'   => $head['EntWaybillNo'],

                'ceb:copNo' => $head['EntEListNo'],

                'ceb:preNo'  => isset($head['preNo']) ? $head['preNo'] : '',
                'ceb:invtNo' => isset($head['invtNo']) ? $head['invtNo'] : '',

                'ceb:ieFlag'   => $this->ieFlag,
                'ceb:portCode' => $head['IEPort'],

                'ceb:ieDate' => $this->sendDay,

                'ceb:statisticsFlag' => $head['statisticsFlag'],
                'ceb:agentCode'      => $head['agent_code'],
                'ceb:agentName'      => $head['agent_name'],

                'ceb:ebcCode' => $head['EBEntNo'],
                'ceb:ebcName' => $head['EBEntName'],

                'ceb:ownerCode'      => $head['ownerCode'],
                'ceb:ownerName'      => $head['ownerName'],
                'ceb:iacCode'        => $head['iacCode'],
                'ceb:iacName'        => $head['iacName'],
                'ceb:emsNo'          => $head['EmsNo'],
                'ceb:tradeMode'      => $head['TradeMode'],
                'ceb:trafMode'       => $head['trafMode'],
                'ceb:trafName'       => empty($head['trafName']) ? '' : $head['trafName'],
                'ceb:voyageNo'       => empty($head['voyageNo']) ? '' : $head['voyageNo'],
                'ceb:billNo'         => empty($head['billNo']) ? '' : $head['billNo'],
                'ceb:totalPackageNo' => empty($head['totalPackageNo']) ? '' : $head['totalPackageNo'],
                'ceb:loctNo'         => empty($head['loctNo']) ? '' : $head['loctNo'],
                'ceb:licenseNo'      => empty($head['licenseNo']) ? '' : $head['licenseNo'],
                'ceb:country'        => $head['OriginCountry'],
                'ceb:POD'            => $head['POD'],
                'ceb:freight'        => $head['freight'],
                'ceb:fCurrency'      => $this->currency,
                'ceb:fFlag'          => $head['fFlag'],
                'ceb:insuredFee'     => $head['insuredFee'],
                'ceb:iCurrency'      => $this->currency,
                'ceb:iFlag'          => $head['iFlag'],
                'ceb:wrapType'       => $head['wrapType'],
                'ceb:packNo'         => $head['MessageId'],
                'ceb:grossWeight'    => $head['grossWeight'],
                'ceb:netWeight'      => $head['netWeight'],
                'ceb:note'           => empty($head['note']) ? '' : $head['note'],
            ];

            if (1 == $declareConfig['OpType']) {
                //增加变更/删除申报字段
                unset($InventoryHeadEle['ceb:preNo'], $InventoryHeadEle['ceb:invtNo']);
            }

            $this->dom = $this->createEle($InventoryHeadEle, $this->dom, $InventoryHead);

            foreach ($list as $key => $value) {
                $InventoryList = $this->dom->createElement('ceb:InventoryList');
                $this->nodeLink['Inventory']->appendchild($InventoryList);

                $InventoryListEle = [
                    'ceb:gnum'         => $key + 1,
                    'ceb:itemNo'       => $value['SKU'],
                    'ceb:itemRecordNo' => $value['EmsNo'],
                    'ceb:itemName'     => $value['GoodsName'],

                    'ceb:gcode'    => $value['gcode'],
                    'ceb:gname'    => $value['gname'],
                    'ceb:gmodel'   => $value['gmodel'],
                    'ceb:barCode'  => $value['BarCode'],
                    'ceb:country'  => $value['OriginCountry'],
                    'ceb:currency' => $this->currency,

                    'ceb:qty'        => $value['qty'],
                    'ceb:qty1'       => $value['qty1'],
                    'ceb:qty2'       => empty($value['qty2']) ? '' : $value['qty2'],
                    'ceb:unit'       => $value['GUnit'],
                    'ceb:unit1'      => $value['StdUnit'],
                    'ceb:unit2'      => empty($value['SecUnit']) ? '' : $value['SecUnit'],
                    'ceb:price'      => (float) $value['GoodsPrice'],
                    'ceb:totalPrice' => $value['TotalPrice'],
                    'ceb:note'       => empty($value['note']) ? '' : $value['note'],
                    // 'ceb:totalPrice' => round((float) $value['GoodsPrice'] * $value['GoodsNumber'], 2),
                    // 'ceb:qty'        => $value['GoodsNumber'],
                    // 'ceb:qty1'       => $value['UnitSum1'] * $value['GoodsNumber'],
                    // 'ceb:qty2'       => empty($value['UnitSum2'] * $value['GoodsNumber']) ? '' : ($value['UnitSum2'] * $value['GoodsNumber']),
                ];

                if (empty($InventoryListEle['ceb:qty2']) || empty($InventoryListEle['ceb:unit2'])) {
                    unset($InventoryListEle['ceb:qty2'], $InventoryListEle['ceb:unit2']);
                }

                $this->dom = $this->createEle($InventoryListEle, $this->dom, $InventoryList);

                $goodsListEle_arr[] = $InventoryListEle;

                //验证数据
                $this->checkInfo($InventoryHeadEle, $goodsListEle_arr);
            }
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
     * 定义验证器来校验清单和清单商品信息.
     */
    public function checkInfo($InventoryHeadEle, $goodsListEle_arr)
    {
        //根据不同的贸易模式, 区分验证规则
        $head_rules = [
            'ceb:logisticsCode' => 'require',
            'ceb:logisticsName' => 'require',
            'ceb:logisticsNo'   => 'require',
            'ceb:ebcCode'       => 'require',
            'ceb:ebcName'       => 'require',
            'ceb:customsCode'   => 'require',
            'ceb:ebpCode'       => 'require',
            'ceb:ebpName'       => 'require',
            'ceb:tradeMode'     => 'require',

            'ceb:orderNo' => 'require|max:60',

            'ceb:copNo'       => 'require|max:30',
            'ceb:logisticsNo' => 'require|max:80',
            'ceb:portCode'    => 'require|max:4',

            'ceb:statisticsFlag' => 'require|max:1',

            'ceb:ownerCode' => 'require|max:18',
            'ceb:ownerName' => 'require|max:100',

            'ceb:trafMode' => 'require|max:1',

            'ceb:country' => 'require',
            'ceb:POD'     => 'require',
            'ceb:freight' => 'require|number',

            'ceb:fFlag'      => 'require|max:1',
            'ceb:insuredFee' => 'require|number',

            'ceb:iFlag'       => 'require|max:1',
            'ceb:wrapType'    => 'require|max:1',
            'ceb:packNo'      => 'require|number',
            'ceb:grossWeight' => 'require|number',
            'ceb:netWeight'   => 'require|number',
        ];

        $list_rules = [
            'ceb:itemNo'       => 'require|max:20',
            'ceb:itemRecordNo' => 'require|max:30',

            'ceb:gcode'  => 'require|max:10',
            'ceb:gname'  => 'require|max:250',
            'ceb:gmodel' => 'require|max:250',

            'ceb:barCode' => 'require|max:50',
            'ceb:country' => 'require|max:3',

            'ceb:qty'  => 'require|number',
            'ceb:qty1' => 'require|number',

            'ceb:unit'  => 'require|max:3',
            'ceb:unit1' => 'require|max:3',

            'ceb:price'      => 'require|number',
            'ceb:totalPrice' => 'require|number',
        ];

        //根据贸易模型选择配置
        if (isset($InventoryHeadEle['ceb:tradeMode'])) {
            if ('9610' == $InventoryHeadEle['ceb:tradeMode']) {
                array_merge($head_rules, [
                    //9610
                    'ceb:iacCode' => 'require|max:18',
                    'ceb:iacName' => 'require|max:100',
                    'ceb:emsNo'   => 'require|max:30',
                ]);
            }
        } else {
            throw new ClientError('报文传输配置, 贸易模式未设置。');
        }

        $this->credentialValidate->setRule($head_rules);

        if (!$this->credentialValidate->check($InventoryHeadEle)) {
            throw new ClientError('报文清单数据: ' . $this->credentialValidate->getError());
        }

        $this->credentialValidate->setRule($list_rules);

        foreach ($goodsListEle_arr as $key => $value) {
            // var_dump($value);
            if (!$this->credentialValidate->check($value)) {
                throw new ClientError('报文清单数据: ' . $this->credentialValidate->getError());
            }
        }

        return true;
    }
}
