<?php

namespace customs\CustomsDeclareClient\CebMessage\EmsLogisticsDeclare;

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
    public $messageType = 'CEB505Message';

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
            'MessageID'    => 'require',
            'FunctionCode' => 'require',
            'MessageType'  => 'require',
            'SenderID'     => 'require',
            'ReceiverID'   => 'require',
            'SendTime'     => 'require',
            'Version'      => 'require',
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
            case 'jx_logistics_declare':
                $result = $this->generateJxFreight($declareConfig, $declareParams);
                break;
            case 'logistics_check':
                $result = $this->freightCheck($declareConfig, $declareParams);
                break;
            case 'logistics_repush':
                $result = $this->freightRepush($declareConfig, $declareParams);
                break;
            case 'logistics_order_declare':
                $result = $this->pushFreightOrder($declareConfig, $declareParams);
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
            'appType'       => 'require',
            'appTime'       => 'require',
            'appStatus'     => 'require',
            'logisticsCode' => 'require',
            'logisticsName' => 'require',
            'logisticsNo'   => 'require',
            'billNo'        => 'require',
            'freight'       => 'require',
            'insuredFee'    => 'require',
            'currency'      => 'require',
            'weight'        => 'require',
            'packNo'        => 'require',
            // 'goodsInfo'          => '',
            'consignee'          => 'require',
            'consigneeAddress'   => 'require',
            'consigneeTelephone' => 'require',
            // 'note'               => '',
            'orderNo' => 'require',
            'ebpCode' => 'require',
        ];

        $this->credentialValidate->setRule($rules);

        if (!$this->credentialValidate->check($LogisticsEle)) {
            throw new ClientError('运单数据: ' . $this->credentialValidate->getError());
        }

        return true;
    }
    /**
     * 定义验证器来校验清单和清单商品信息.
     */
    public function checkJxInfo($LogisticsEle)
    {
        $rules = [
            'appType'       => 'require',
            'appTime'       => 'require',
            'logisticsCode' => 'require',
            'logisticsName' => 'require',
            'logisticsNo'   => 'require',
            'freight'       => 'require',
            'insuredFee'    => 'require',
            'currency'      => 'require',
            'weight'        => 'require',
            'packNo'        => 'require',
            'consignee'          => 'require',
            'consigneeAddress'   => 'require',
            'consigneeTelephone' => 'require',
            'orderNo' => 'require',
            'ebpCode' => 'require',
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
        $root_node = $this->dom->createElement('Manifest');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);

        $HeadEle = [
            'MessageID'    => $declareConfig['MessageID'],
            'FunctionCode' => $declareConfig['FunctionCode'],
            'MessageType'  => $declareConfig['MessageType'],
            'SenderID'     => $declareConfig['SenderID'],
            'ReceiverID'   => $declareConfig['ReceiverID'],
            'SendTime'     => $declareConfig['SendTime'],
            'Version'      => $declareConfig['Version'],
        ];

        $this->dom = $this->createEle($HeadEle, $this->dom, $head);

        $declaration_node = $this->dom->createElement('Declaration');
        $this->nodeLink['root_node']->appendchild($declaration_node);

        $freight_node = $this->dom->createElement('Freights');
        $declaration_node->appendchild($freight_node);

        //一个报文可以又多个订单
        foreach ($declareParams as $key => $value) {
            $Freight = $this->dom->createElement('Freight');
            $freight_node->appendchild($Freight);
            $this->nodeLink['Freights'] = $Freight;

            $FreightEle = [
                'appType'            => $value['appType'],
                'appTime'            => $value['appTime'],
                'appStatus'          => $value['appStatus'],
                'logisticsCode'      => $value['logisticsCode'],
                'logisticsName'      => $value['logisticsName'],
                'logisticsNo'        => $value['logisticsNo'],
                'billNo'             => $value['billNo'],
                'freight'            => $value['freight'],
                'insuredFee'         => $value['insuredFee'],
                'currency'           => $value['currency'],
                'weight'             => $value['weight'],
                'packNo'             => $value['packNo'],
                'goodsInfo'          => $value['goodsInfo'],
                'consignee'          => $value['consignee'],
                'consigneeAddress'   => $value['consigneeAddress'],
                'consigneeTelephone' => $value['consigneeTelephone'],
                'note'               => $value['note'],
                'orderNo'            => $value['orderNo'],
                'ebpCode'            => $value['ebpCode'],
            ];

            $this->dom = $this->createEle($FreightEle, $this->dom, $Freight);

            $KzInfo_node = $this->dom->createElement('KzInfo');
            $this->nodeLink['Freights']->appendchild($KzInfo_node);

            $KzInfoEle = $value['KzInfo'];

            $this->dom = $this->createEle($KzInfoEle, $this->dom, $KzInfo_node);

            //验证数据
            $this->checkInfo($FreightEle);
        }

        return $this->dom->saveXML();
    }

    //生成进口运单申报报文
    public function generateJxFreight($declareConfig, $declareParams)
    {
        //根节点生成--父类
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Manifest');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);

        $HeadEle = [
            'MessageID'    => $declareConfig['MessageID'],
            'FunctionCode' => $declareConfig['FunctionCode'],
            'MessageType'  => $declareConfig['MessageType'],
            'SenderID'     => $declareConfig['SenderID'],
            'ReceiverID'   => $declareConfig['ReceiverID'],
            'SendTime'     => $declareConfig['SendTime'],
            'Version'      => $declareConfig['Version'],
        ];

        $this->dom = $this->createEle($HeadEle, $this->dom, $head);

        $declaration_node = $this->dom->createElement('Declaration');
        $this->nodeLink['root_node']->appendchild($declaration_node);

        $freight_node = $this->dom->createElement('Freights');
        $declaration_node->appendchild($freight_node);

        //一个报文可以又多个订单
        foreach ($declareParams as $key => $value) {
            $Freight = $this->dom->createElement('Freight');
            $freight_node->appendchild($Freight);
            $this->nodeLink['Freights'] = $Freight;

            $FreightEle = [
                'appType'            => $value['appType'],
                'appTime'            => $value['appTime'],
                'logisticsCode'      => $value['logisticsCode'],
                'logisticsName'      => $value['logisticsName'],
                'logisticsNo'        => $value['logisticsNo'],
                'freight'            => $value['freight'],
                'insuredFee'         => $value['insuredFee'],
                'currency'           => $value['currency'],
                'weight'             => $value['weight'],
                'packNo'             => $value['packNo'],
                'goodsInfo'          => $value['goodsInfo'],
                'consignee'          => $value['consignee'],
                'consigneeAddress'   => $value['consigneeAddress'],
                'consigneeTelephone' => $value['consigneeTelephone'],
                'note'               => $value['note'],
                'orderNo'            => $value['orderNo'],
                'ebpCode'            => $value['ebpCode'],
            ];

            $this->dom = $this->createEle($FreightEle, $this->dom, $Freight);

            $KzInfo_node = $this->dom->createElement('KzInfo');
            $this->nodeLink['Freights']->appendchild($KzInfo_node);

            $KzInfoEle = $value['KzInfo'];

            $this->dom = $this->createEle($KzInfoEle, $this->dom, $KzInfo_node);

            //验证数据
            $this->checkJxInfo($FreightEle);
        }

        return $this->dom->saveXML();
    }

    public function freightCheck($declareConfig, $declareParams)
    {
        //根节点生成--父类
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Manifest');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);

        $HeadEle = [
            'MessageID'    => $declareConfig['MessageID'],
            'FunctionCode' => $declareConfig['FunctionCode'],
            'MessageType'  => $declareConfig['MessageType'],
            'SenderID'     => $declareConfig['SenderID'],
            'ReceiverID'   => $declareConfig['ReceiverID'],
            'SendTime'     => $declareConfig['SendTime'],
            'Version'      => $declareConfig['Version'],
        ];

        $this->dom = $this->createEle($HeadEle, $this->dom, $head);

        $declaration_node = $this->dom->createElement('Declaration');
        $this->nodeLink['root_node']->appendchild($declaration_node);

        $freight_no_node = $this->dom->createElement('FreightNos');
        $declaration_node->appendchild($freight_no_node);

        //一个报文可以又多个订单
        foreach ($declareParams as $value) {
            $note = $this->dom->createElement('freightNo');
            $freight_no_node->appendchild($note);
            $zhi = $this->dom->createTextNode($value);
            $note->appendchild($zhi);
        }

        return $this->dom->saveXML();
    }

    //电子运单信息重推--重推指ems重推旧的报文数据给海关，修改不了报文数据
    public function freightRepush($declareConfig, $declareParams)
    {
        //根节点生成--父类
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Manifest');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);

        $HeadEle = [
            'MessageID'    => $declareConfig['MessageID'],
            'FunctionCode' => $declareConfig['FunctionCode'],
            'MessageType'  => $declareConfig['MessageType'],
            'SenderID'     => $declareConfig['SenderID'],
            'ReceiverID'   => $declareConfig['ReceiverID'],
            'SendTime'     => $declareConfig['SendTime'],
            'Version'      => $declareConfig['Version'],
        ];

        $this->dom = $this->createEle($HeadEle, $this->dom, $head);

        $declaration_node = $this->dom->createElement('Declaration');
        $this->nodeLink['root_node']->appendchild($declaration_node);

        $freight_no_node = $this->dom->createElement('FreightNos');
        $declaration_node->appendchild($freight_no_node);

        //一个报文可以又多个订单
        foreach ($declareParams as $value) {
            $note = $this->dom->createElement('freightNo');
            $freight_no_node->appendchild($note);
            $zhi = $this->dom->createTextNode($value);
            $note->appendchild($zhi);
        }

        return $this->dom->saveXML();
    }

    //EMS收寄订单信息推送报文
    public function pushFreightOrder($declareConfig, $declareParams)
    {
        //根节点生成--父类
        $this->dom = new \DomDocument('1.0', 'UTF-8');
        $root_node = $this->dom->createElement('Manifest');
        $this->dom->appendchild($root_node);

        //组装头部
        $this->nodeLink['root_node'] = $root_node;
        $head                        = $this->dom->createElement('Head');
        $this->nodeLink['root_node']->appendchild($head);

        $HeadEle = [
            'MessageID'    => $declareConfig['MessageID'],
            'FunctionCode' => $declareConfig['FunctionCode'],
            'MessageType'  => $declareConfig['MessageType'],
            'SenderID'     => $declareConfig['SenderID'],
            'ReceiverID'   => $declareConfig['ReceiverID'],
            'SendTime'     => $declareConfig['SendTime'],
            'Version'      => $declareConfig['Version'],
        ];

        $this->dom = $this->createEle($HeadEle, $this->dom, $head);

        $declaration_node = $this->dom->createElement('Declaration');
        $this->nodeLink['root_node']->appendchild($declaration_node);

        $orders_node = $this->dom->createElement('OrderPkgs');
        $declaration_node->appendchild($orders_node);

        //一个报文可以又多个订单
        foreach ($declareParams as $key => $value) {
            $order_node = $this->dom->createElement('OrderPkg');
            $orders_node->appendchild($order_node);

            $OrderEle = [
                'appType'         => $value['appType'],
                'orderNo'         => $value['orderNo'],
                'accountBookNo'   => $value['accountBookNo'],
                'inOutDate'       => $value['inOutDate'],
                'inOutPortCode'   => $value['inOutPortCode'],
                'declareDate'     => $value['declareDate'],
                'arrivedPort'     => $value['arrivedPort'],
                'ebcCode'         => $value['ebcCode'],
                'ebcName'         => $value['ebcName'],
                'logisticsNo'     => $value['logisticsNo'],
                'fromCountry'     => $value['fromCountry'],
                'roughWeight'     => $value['roughWeight'],
                'netWeight'       => $value['netWeight'],
                'packType'        => $value['packType'],
                'declarePortCode' => $value['declarePortCode'],
                'goodsYardCode'   => $value['goodsYardCode'],
                'sender'          => $value['sender'],
                'receiver'        => $value['receiver'],
                'senderCountry'   => $value['senderCountry'],
                'senderCity'      => $value['senderCity'],
                'worth'           => $value['worth'],
                'majorGoodName'   => $value['majorGoodName'],
                'monitorDeclFlag' => $value['monitorDeclFlag'],
                'orgCode'         => $value['orgCode'],
                'remark'          => $value['remark'],
            ];

            $this->dom = $this->createEle($OrderEle, $this->dom, $order_node);

            $goods_node = $this->dom->createElement('OrderPkgGoods');
            $order_node->appendchild($goods_node);

            foreach ($value['OrderPkgGood'] as $k => $v) {
                $good_node = $this->dom->createElement('OrderPkgGood');
                $goods_node->appendchild($good_node);
                $GoodsEle = [
                    'gnum'         => $v['gnum'],
                    'gname'        => $v['gname'],
                    'gmodel'       => $v['gmodel'],
                    'country'      => $v['country'],
                    'declarePrice' => $v['declarePrice'],
                    'declareCount' => $v['declareCount'],
                    'declareUnit'  => $v['declareUnit'],
                    'gRoughWeight' => $v['gRoughWeight'],
                    'gRecordCode'  => $v['gRecordCode'],
                    'websiteHref'  => $v['websiteHref'],
                    'mailTaxNo'    => $v['mailTaxNo'],
                    'hsCode'       => $v['hsCode'],
                    'gcode'        => $v['gcode'],
                    'gTotalPrice'  => $v['gTotalPrice'],
                    'gRemark'      => $v['gRemark'],
                ];

                $this->dom = $this->createEle($GoodsEle, $this->dom, $good_node);
            }
        }

        return $this->dom->saveXML();
    }
}
