<?php

namespace customs\CustomsDeclareClient\GzWindowKjMessage\GoodsCross;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;
use customs\CustomsDeclareClient\Base\GzWindowKjMessageBuild;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    use GzWindowKjMessageBuild;

    //本报文编号
    public $messageType = 'KJ881101';

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
     * 进出口货物申报.
     *
     * @throws ClientError
     */
    public function declare(array $declareConfig, array $declareParams)
    {
        $this->credentialValidate->setRule(
            [
                'MessageID'    => 'require|max:36',
                'MessageType'  => 'require|max:36',
                'Sender'       => 'require|max:36',
                'Receiver'     => 'require|max:36',
                'FunctionCode' => 'require|max:36',
                'BusinessType' => 'require|max:10',
                'IeFlag'       => 'require|max:1',
                'OpType'       => 'require|max:1',

                'DeclEntNo'   => 'require|max:50',
                'DeclEntName' => 'require|max:100',
                'EBEntNo'     => 'require|max:50',
                'EBEntName'   => 'require|max:100',
                'CustomsCode' => 'require|max:50',
                'CIQOrgCode'  => 'require|max:50',
                'EBPEntNo'    => 'require|max:50',
                'EBPEntName'  => 'require|max:100',
            ]
        );

        if (!$this->credentialValidate->check($declareConfig)) {
            throw new ClientError('报文传输配置' . $this->credentialValidate->getError());
        }

        if (isset($declareConfig['OpType'])) {
            if (2 == $declareConfig['OpType']) {
                $this->sendTime = date('YmdHis', (time() + 1000));
            }
            $this->opType = $declareConfig['OpType'];
        } else {
            $this->sendTime = date('YmdHis', time());
            $this->opType   = 1;
        }

        //搜索申请单信息
        $goodsInfo = $declareParams['goods_info'];

        //生成XML头部结构
        $dom = new \DomDocument('1.0', 'utf-8');

        $InternationalTrade = $dom->createElement('InternationalTrade');
        $dom->appendchild($InternationalTrade);
        $Head = $dom->createElement('Head');
        $InternationalTrade->appendchild($Head);
        $Declaration = $dom->createElement('Declaration');
        $InternationalTrade->appendchild($Declaration);

        //生成商品备案包报文
        $element = [
            'MessageID'    => $declareConfig['MessageID'],
            'MessageType'  => $declareConfig['MessageType'],
            'Sender'       => $declareConfig['Sender'],
            'Receiver'     => $declareConfig['Receiver'],
            'SendTime'     => $this->sendTime,
            'FunctionCode' => $declareConfig['FunctionCode'],
            'SignerInfo'   => '',
            'Version'      => $this->version,
        ];

        $dom = $this->createEle($element, $dom, $Head);

        $regHeadEle = [
            'DeclEntNo'   => $declareConfig['DeclEntNo'],
            'DeclEntName' => $declareConfig['DeclEntName'],
            'EBEntNo'     => $declareConfig['EBEntNo'],
            'EBEntName'   => $declareConfig['EBEntName'],
            'OpType'      => $this->opType,
            'CustomsCode' => $declareConfig['CustomsCode'],
            'CIQOrgCode'  => $declareConfig['CIQOrgCode'],
            'EBPEntNo'    => $declareConfig['EBPEntNo'],
            'EBPEntName'  => $declareConfig['EBPEntName'],

            'CurrCode'     => $this->currency,
            'BusinessType' => $declareConfig['BusinessType'],
            'DeclTime'     => $this->sendTime,
            'InputDate'    => $this->sendTime,
            'IeFlag'       => $declareConfig['IeFlag'],
        ];

        $GoodsRegHead = $dom->createElement('GoodsRegHead');
        $Declaration->appendchild($GoodsRegHead);
        $dom = $this->createEle($regHeadEle, $dom, $GoodsRegHead);

        $GoodsRegList = $dom->createElement('GoodsRegList');
        $Declaration->appendchild($GoodsRegList);

        foreach ($goodsInfo as $key => $value) {
            $GoodsContent = $dom->createElement('GoodsContent');
            $GoodsRegList->appendchild($GoodsContent);

            $goodsEle = [
                'Seq'            => sprintf('%03d', $key + 1),
                'EntGoodsNo'     => $value['EntGoodsNo'] . '_' . $declareConfig['CIQOrgCode'], //商品自编号
                'EPortGoodsNo'   => $value['EPortGoodsNo'],
                'CIQGoodsNo'     => $value['CIQGoodsNo'],
                'CusGoodsNo'     => $value['CusGoodsNo'],
                'EmsNo'          => '',
                'ItemNo'         => '',
                'ShelfGName'     => $value['ShelfGName'],
                'NcadCode'       => $value['NcadCode'],
                'HSCode'         => $value['HSCode'],
                'BarCode'        => $value['BarCode'],
                'GoodsName'      => $value['GoodsName'],
                'GoodsStyle'     => $value['GoodsStyle'],
                'Brand'          => $value['Brand'],
                'GUnit'          => $value['GUnit'],
                'StdUnit'        => $value['StdUnit'],
                'SecUnit'        => $value['SecUnit'],
                'RegPrice'       => $value['RegPrice'],
                'GiftFlag'       => $value['GiftFlag'],
                'OriginCountry'  => $value['OriginCountry'],
                'Quality'        => $value['Quality'],
                'QualityCertify' => '',
                'Manufactory'    => $value['Manufactory'],
                'NetWt'          => $value['NetWt'],
                'GrossWt'        => $value['GrossWt'],
                'Notes'          => '',
            ];

            $this->checkGoodsInfo($goodsEle);

            $dom = $this->createEle($goodsEle, $dom, $GoodsContent);
        }

        return $dom->saveXML();
    }

    /**
     * 定义验证器来校验商品信息.
     */
    public function checkGoodsInfo($data)
    {
        $this->credentialValidate->setRule([
            'EntGoodsNo'     => 'require|max:20',
            'EPortGoodsNo'   => 'max:60',
            'CIQGoodsNo'     => 'max:36',
            'CusGoodsNo'     => 'max:50',
            'EmsNo'          => 'max:255',
            'ItemNo'         => 'max:255',
            'ShelfGName'     => 'require|max:255',
            'NcadCode'       => 'require|max:8',
            'HSCode'         => 'require|max:10',
            'BarCode'        => 'max:20',
            'GoodsName'      => 'require|max:255',
            'GoodsStyle'     => 'require|max:255',
            'Brand'          => 'require|max:50',
            'GUnit'          => 'require|number|max:3',
            'StdUnit'        => 'require|number|max:3',
            'SecUnit'        => 'max:3',
            'RegPrice'       => 'require|number',
            'GiftFlag'       => 'require|number|max:1',
            'OriginCountry'  => 'require|number|max:3',
            'Quality'        => 'require|max:100',
            'QualityCertify' => 'max:100',
            'Manufactory'    => 'require|max:255',
            'NetWt'          => 'require|number',
            'GrossWt'        => 'require|number',
        ]);

        if (!$this->credentialValidate->check($data)) {
            throw new ClientError('商品数据: 商品实际总值与订单记录不符。');
        }

        return true;
    }
}
