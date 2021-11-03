<?php

require_once './vendor/autoload.php';

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareService\OrderCrossImportService;
use customs\CustomsDeclareService\GoodsCrossService;
use customs\CustomsDeclareService\ChecklistCrossImportService;
use customs\CustomsTradePostFactory;
use customs\CustomsDeclareService\RsaSignService;
use customs\CustomsDeclareService\HttpMessageDeclareService;

$ioc_con_app = new Application();

//ceb进口清单申报服务-----
//$declareSrv = new ChecklistCrossImportService($ioc_con_app);
$factory = new CustomsTradePostFactory();
$rsa_sign = new RsaSignService($ioc_con_app);

$declareSrv = $factory->getInstance('ChecklistCrossImportService', $ioc_con_app);


//$declareSrv = new OrderCrossImportService($ioc_con_app);

// $param['order_info'] = [
//     'EntOrderNo' =>  '123',
//     'OrderGoodTotal' =>  '24',
//     'Freight' =>  '0',
//     'OtherPayment' =>  '0',
//     'Tax' =>  '0.5',
//     'ActualAmountPaid' =>  '24.5',
//     'OrderDocAcount' =>  '123',
//     'OrderDocName' =>  '123',
//     'OrderDocTel' =>  '123',
//     'OrderDocType' =>  '1',
//     'OrderDocId' =>  '123',
//     'RecipientName' =>  '123',
//     'RecipientTel' =>  '123',
//     'RecipientAddr' =>  '123'
// ];

// $param['goods_info'][] = [
//     'SKU' => '1232',
//     'GoodsName' => '1232',
//     'GoodsStyle' => '1232',
//     'BarCode' => '1232',
//     'GUnit' => '1232',
//     'GoodsNumber' => '2',
//     'RegPrice' => '12'
// ];

// $config = [
//     'MessageId' => 're',
//     'EBPEntNo'  => 're',
//     'EBPEntName' => 'req',
//     'EBEntNo'    => 're',
//     'EBEntName'   => 'req',
//     'DeclEntNo'   => 're',
//     'DeclEntName' => 'req',
//     'DeclEntDxpid' => 're',
//     'opType'       => '1',
// ];

// var_dump($declareSrv->declare($config, $param));
// die();

// $param['goods_info'][] = [
//     'Seq'           => '1',
//     'EntGoodsNo'    => '1',
//     'EPortGoodsNo'  => '1',
//     'CIQGoodsNo'    => '1',
//     'CusGoodsNo'    => '1',
//     'EmsNo'         => '1',
//     'ItemNo'        => '1',
//     'ShelfGName'    => '1',
//     'NcadCode'      => '1',
//     'HSCode'        => '1',
//     'BarCode'       => '1',
//     'GoodsName'     => '1',
//     'GoodsStyle'    => '1',
//     'Brand'         => '1',
//     'GUnit'         => '1',
//     'StdUnit'       => '1',
//     'SecUnit'       => '1',
//     'RegPrice'      => '1',
//     'GiftFlag'      => '1',
//     'OriginCountry' => '1',
//     'Quality'       => '1',
//     'QualityCertify'=> '1',
//     'Manufactory'   => '1',
//     'NetWt'         => '1',
//     'GrossWt'       => '1',
//     'Notes'         => '1'
// ];  

// $config = [
//     'MessageID' => '1',
//     'MessageType' => '1',
//     'Sender' => '1',
//     'Receiver' => '1',
//     'FunctionCode' => '1',
//     'DeclEntNo' => '1',
//     'DeclEntName' => '1',
//     'EBEntNo' => '1',
//     'EBEntName' => '1',
//     'CustomsCode' => '1',
//     'CIQOrgCode' => '1',
//     'EBPEntNo' => '1',
//     'EBPEntName' => '1',
//     'BusinessType' => '1',
//     'IeFlag' => '1'
// ];


//清单数据。
$param['checklist_info'] = [
    'EntOrderNo' => 'require',
    'EntEListNo' => 'require',
    'OrderDocId' => 'require',
    'OrderDocName' =>'require',
    'OrderDocTel' => 'require',
    'RecipientAddr' => 'require',
    'trafCode' => '1213',
    'ShipperCountryCode' => '1213',
    'FeeRate' => '1213',
    'InsurRate' => '1213',
    'WrapType' =>'1213',
    'TotalGrossWeight' =>'1213',
    'TotalNetWeight' => '1213',
    'RecipientTel'=> '1213',
    'EntWaybillNo'=> '1213',
];

$param['goods_info'][] = [

    'SKU' => 'require',
    'GoodsName' => 'require',
    'HSCode' => 'require',
    'GoodsName' => 'require',
    'GoodsStyle' => 'require',
    'BarCode' => '213131',
    'OriginCountry' => '1313',
    'GoodsNumber' => '1313',
    'GUnit' => 'require',
    'UnitSum1' => '1313',
    'UnitSum2'=> '1313',
    'StdUnit' => '1313',
    'SecUnit' => '1313',
    'GoodsPrice' => '1313',

];

$config = [
        //修改
        'PreNo'      => 'max:50',
        'CusEListNo' => 'max:18',

        'EBPEntNo'   => 'require',
        'EBPEntName' => 'require',

        'EBEntNo'   => 'require',
        'EBEntName' => 'require',

        'EHSEntNo'   => 'require',
        'EHSEntName' => 'require',

        'DanBaoEntNo' => 'require',

        'CustomsCode' => 'require',
        'IEPort'      => 'require',

        'TradeMode' =>'1210',
        'SvPCode'   => 'require',

        'DeclEntNo'    => 'require',
        'DeclEntName'  => 'require',
        'DeclEntDxpid' => 'require',

        'MessageId'    => 'require',

        'OpType'    => 'require',

        //1210
        'AreaCode' => 'require',
        'AreaName' => 'require',
        'EmsNo' => 'require',
];

$key = '-----BEGIN RSA PRIVATE KEY-----
MIICXQIBAAKBgQChU4mgtA+S8rPt7S/rvytowmXv9anWxXo03AYUHzVSMt8DDBF5
T58lbpI9lajtoJl1EpMJlBAhfq4eYX61jtjvwyF2JfGcQPU1uFLuQ625YHaa5YIh
yo+mJuyNh3JIh488fe14xh5petZ+gFze4hteKRBfU8UWhsqySenkmhF2GwIDAQAB
AoGAAsi5SE+zMRsFJecA+7WJ3z3zmmeH7c/sovrlE/XR6sA7/wZ3SruoCHJXDb8G
cktiOpX+eZzXhmx0Psv57tIvRjT3U05DcYYqHQWRbYxNAZTZgRUuF6vbRD3zHIBv
Nwiwo4dDfoDry7APJ2O62vkALpjB/iCEFrn62y62Q01KM6ECQQDUv7hCv7GL+Ox9
dZQvr5ATGBma7V2HTYnkMoabEgsem3uPNamEYzEJmfkiS9Fu9Y+rFY53TnRHSj0o
JEBSIkiLAkEAwh+WJKqbzNWJz7M5i5sMIsvYu7c0iwZVBnAipAIzBPryhl3Gj8D3
vVNH07Zaqstbv0LSGqkN+W2iReMQWbOqsQJADi0kSxZY71nL8GQ4VqEdOZh+hEtS
0yRAjvsq6wRdx1FW/2j0/cRaTSh8aGOi9gDY7O6Hyr1olCOHRbh8rLxqFwJBAKVc
qNa1KqDTamCuqJ9+xVjC0u4dAzpGe5lPXoLdiL5+UqgZK/L7C06Qqcf0N8n0D0Se
1EF3rvrB5JCp+xyeixECQQDTTljTaU/3eB+xuOUYJk/ohBT78R1uf+PGiHNdZhyu
IoSofAkeel9d4dhSHa3OJru0Un7dNq/kcm9b2PA9VB9C
-----END RSA PRIVATE KEY-----
';

// var_dump($declareSrv->declare($config, $param));
// die();

$base = [

    'MessageId'=>'CEB403Message',
    'Sender' => 'TEST17',
    'Receiver' => 'TEST17',
    'FileName' => '待定'
];
$http_message = new HttpMessageDeclareService($ioc_con_app);
//  var_dump($http_message->generatePublicHttpMessage($base,$declareSrv->generateXmlPost($config, $param), 'CEB403Message', $key));
// die();
 var_dump($declareSrv->generateHttpDoc($config, $param, $base, $key));
die();


$test01 =  $payReceiveCrossExportService->generateXmlPost($declareConfig, $declareParams);


$test02 = $payReceiveCrossExportService->generateHttpDoc('CEB403Message', $test01, $base);
var_dump($test01);
var_dump($test02);
var_dump($rsaSignService->sign($key, $test02));
die();