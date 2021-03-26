<?php

require_once __DIR__ . '/vendor/autoload.php';

use customs\CustomsDeclareClient\Application;
use customs\CustomsTradePostFactory;

$ioc_con_app = new Application();

//江西综服进口订单申报
$customsTradePostFactory = new CustomsTradePostFactory();
$obj                     = $customsTradePostFactory->getInstance('JxCrossImportService', $ioc_con_app);

/**
 * 江西综服进口订单申报.
 */
$customsTradePostFactory = new CustomsTradePostFactory();
$obj                     = $customsTradePostFactory->getInstance('JxCrossImportService', $ioc_con_app);

$declareConfig = [
    'OpType'     => '1',
    'EBPEntNo'   => '3607W60007', //电商平台在跨境电商综合服务平台的备案名称
    'EBPEntName' => '123123', //电商平台在跨境电商综合服务的备案编号
    'EBEntNo'    => '123123', //电商企业编码
    'EBEntName'  => '112312323', //电商企业名称
];

$declareParams[] = [
    'order_info' => [
        'businessNo' => '11111111111',

        'payType'          => '03',
        'payCompanyCode'   => '1233123',
        'payCompanyName'   => '招行',
        'payNumber'        => '3333333333333333',
        'ActualAmountPaid' => 100,
        'orderTotalAmount' => 100,
        'EntOrderNo'       => '123232323',
        'Tax'              => 0,
        'OrderGoodTotal'   => 100,
        // 'feeAmount' => '', //非必
        'insureAmount' => 0,
        'tradeTime'    => '1611236888',
        'currency'     => '142',
        // 'totalAmount' => '',
        // 'consigneeEmail' => '',
        'RecipientTel'  => '13424499957',
        'RecipientName' => '杨斯祺',
        'RecipientAddr' => '浙江省杭州市下城区石桥街道 元都新景公寓16号2单元1301',
        'totalCount'    => 1,
        // 'postMode' => '',
        'senderCountry'    => '142',
        'senderName'       => 'Tony',
        'OrderDocAcount'   => 'bbbiit',
        'LogisCompanyName' => '江西省邮政快递服务有限公司',
        'LogisCompanyCode' => '3601984979',
        // 'zipCode' => '',
        // 'wayBills' =>'',
        // 'rate' =>'',
        'OtherPayment' => 0,
        'OrderDocName' => '杨斯祺',
        'OrderDocTel'  => '13424499957',
        'OrderDocId'   => '440883199706221444',
    ],
    'goods_info' => [
        [
            'GoodsName'     => '化妆水',
            'SKU'           => '9969696900',
            'GoodsStyle'    => '100ml',
            'OriginCountry' => '中国',
            'RegPrice'      => 100,
            'currency'      => '142',
            'GoodsNumber'   => 1,
            'GUnit'         => '311',
            'BarCode'       => '898989898989',
        ],
    ],
];
$declareParams[] = [
    'order_info' => [
        'payType'          => '03',
        'payCompanyCode'   => '1233123',
        'payCompanyName'   => '招行',
        'payNumber'        => '3333333333333333',
        'ActualAmountPaid' => 100,
        'orderTotalAmount' => 100,
        'EntOrderNo'       => '123232323',
        'Tax'              => 0,
        'OrderGoodTotal'   => 100,
        // 'feeAmount' => '', //非必
        'insureAmount' => 0,
        'tradeTime'    => '1611236888',
        'currency'     => '142',
        // 'totalAmount' => '',
        // 'consigneeEmail' => '',
        'RecipientTel'  => '13424499957',
        'RecipientName' => '杨斯祺',
        'RecipientAddr' => '浙江省杭州市下城区石桥街道 元都新景公寓16号2单元1301',
        'totalCount'    => 1,
        // 'postMode' => '',
        'senderCountry'    => '142',
        'senderName'       => 'Tony',
        'OrderDocAcount'   => 'bbbiit',
        'LogisCompanyName' => '江西省邮政快递服务有限公司',
        'LogisCompanyCode' => '3601984979',
        // 'zipCode' => '',
        // 'wayBills' =>'',
        // 'rate' =>'',
        'OtherPayment' => 0,
        'OrderDocName' => '杨斯祺',
        'OrderDocTel'  => '13424499957',
        'OrderDocId'   => '440883199706221444',
    ],
    'goods_info' => [
        [
            'GoodsName'     => '化妆水',
            'SKU'           => '9969696900',
            'GoodsStyle'    => '100ml',
            'OriginCountry' => '中国',
            'RegPrice'      => 100,
            'currency'      => '142',
            'GoodsNumber'   => 1,
            'GUnit'         => '311',
            'BarCode'       => '898989898989',
        ],
    ],
];
// $result = $obj->generateOrderXmlPost($declareConfig, $declareParams);
// var_dump($result); die();

/**
 * 清单申报.
 */
$declareConfig = [
    'OpType'     => '1',
    'EBPEntNo'   => '3607W60007', //电商平台在跨境电商综合服务平台的备案名称
    'EBPEntName' => '123123', //电商平台在跨境电商综合服务的备案编号
    'EBEntNo'    => '123123', //电商企业编码
    'EBEntName'  => '112312323', //电商企业名称

    'importType' => '0',
    'iePort'     => '1210',
    'declPort'   => '1210',

    'DeclareCompanyType' => '外资',
    'DeclEntNo'          => 'decl_ent_no',
    'DeclEntName'        => 'decl_ent_name',

    'enteringPerson'      => 'decl_ent_no',
    'enteringCompanyName' => 'decl_ent_name',
    'AssureCode'          => 'assureCode',
    'AreaEntNo'           => '',
    'AreaEntName'         => '',
];

$declareParams2[] = [
    'order_info' => [
        'businessNo' => '11111111111',
        'preEntryNumber'  => '111111111',
        'inOutDateStr'       => '',
        'iePort'             => '',
        'destinationPort'    => '',
        'trafName'           => '',
        'voyageNo'           => '',
        'trafNo'             => '',
        'trafMode'           => '',
        'logisCompanyName'   => '',
        'logisCompanyCode'   => '',
        'EntOrderNo'         => '',
        'wayBill'            => '',
        'wayBill'            => '',
        'tradeCountry'       => '',
        'packNo'             => '',
        'grossWeight'        => '',
        'netWeight'          => '',
        'warpType'           => '',
        'customsField'       => '',
        'senderName'         => '',
        'consignee'          => '',
        'senderCountry'      => '',
        'senderCity'         => '',
        'consigneeAddress'   => '',
        'purchaserTelNumber' => '',
        'buyerIdType'        => '',
        'buyerIdNumber'      => '',
        'buyerName'          => '',
        'worth'              => '',
        'feeAmount'          => '',
        'insureAmount'       => '',
        'currency'           => '',
        'mainGName'          => '',
        'assureCode'         => '',
    ],
    'goods_info' => [
        [
            'SKU'            => '',
            'goodsItemNo'    => '',
            'itemRecordNo'   => '',
            'GoodsName'      => '',
            'GoodsStyle'     => '',
            'OriginCountry'  => '',
            'currency'       => '',
            'RegPrice'       => '',
            'declTotalPrice' => '',
            'GoodsNumber'    => '',
            'GUnit'          => '',
            'firstUnit'      => '',
            'firstCount'     => '',
            'tradeCountry'   => '',
        ],
    ],
];
$declareParams2[] = [
    'order_info' => [
        'businessNo' => '11111111111',
        'preEntryNumber'  => '111111111',
        'inOutDateStr'       => '',
        'iePort'             => '',
        'destinationPort'    => '',
        'trafName'           => '',
        'voyageNo'           => '',
        'trafNo'             => '',
        'trafMode'           => '',
        'logisCompanyName'   => '',
        'logisCompanyCode'   => '',
        'EntOrderNo'         => '',
        'wayBill'            => '',
        'wayBill'            => '',
        'tradeCountry'       => '',
        'packNo'             => '',
        'grossWeight'        => '',
        'netWeight'          => '',
        'warpType'           => '',
        'customsField'       => '',
        'senderName'         => '',
        'consignee'          => '',
        'senderCountry'      => '',
        'senderCity'         => '',
        'consigneeAddress'   => '',
        'purchaserTelNumber' => '',
        'buyerIdType'        => '',
        'buyerIdNumber'      => '',
        'buyerName'          => '',
        'worth'              => '',
        'feeAmount'          => '',
        'insureAmount'       => '',
        'currency'           => '',
        'mainGName'          => '',
        'assureCode'         => '',
    ],
    'goods_info' => [
        [
            'SKU'            => '',
            'goodsItemNo'    => '',
            'itemRecordNo'   => '',
            'GoodsName'      => '',
            'GoodsStyle'     => '',
            'OriginCountry'  => '',
            'currency'       => '',
            'RegPrice'       => '',
            'declTotalPrice' => '',
            'GoodsNumber'    => '',
            'GUnit'          => '',
            'firstUnit'      => '',
            'firstCount'     => '',
            'tradeCountry'   => '',
        ],
    ],
];
$customsTradePostFactory = new CustomsTradePostFactory();
$obj                     = $customsTradePostFactory->getInstance('JxImportListService', $ioc_con_app);

$result = $obj->generateListXmlPost($declareConfig, $declareParams2);
var_dump($result); die();

$uri      = 'http://122.224.230.26:20093/newyorkWS/ws/ReceiveEncryptDeclare?wsdl';
$msgType  = 'IMPORTORDER';
$sendCode = '3607W60007';
$aes_key  = 'JXdM13bI4JyKOXyBwZIRQw==';
$rsa_key  = 'MIIBVAIBADANBgkqhkiG9w0BAQEFAASCAT4wggE6AgEAAkEAk9RzYOprnEq/F6szOpoZwFOH5rOoEALO8QG6uxdPE5wFI8CsBD4Ezy6a4nNGEpgxTHDFbdvhudfICoKJiJd5MwIDAQABAkBqPo0xwSjX6gyOuTcXTftl82K/1qzZ3PrX0ZNftS/a+fWXGwzZH7GcnzyQIAeP9yPnxQrTX85bvhXq9NImAJqBAiEA0HwlLRVSCn1GQoyHxOJeS5a4ySNl0VUUQKGrYWlAmEECIQC1hXGzL4gac2IlMczfu4OJ33ohaRh3lmDwA4f0mS4UcwIgFxurgzO5xC/eecHZypjMmtQ55xFlV652cDN7K3DfGAECIHSoDAhCNc/580tAFBB9K+4BZzXtmsHQLQBS5J73irutAiEAwHp3899KO4mBXM6y8GSH5Zsh7kHqxUjQ//KTIsseqRA=';

//报文内容aes加密：
$content = openssl_encrypt($xml, 'AES-128-ECB', base64_decode($aes_key));

//数字签名
$privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
        wordwrap($rsa_key, 64, "\n", true) .
        "\n-----END RSA PRIVATE KEY-----";
$key = openssl_get_privatekey($privateKey);
openssl_sign($xml, $dataDigest, $key);
$dataDigest = base64_encode($dataDigest);

// var_dump([
// 	'content'=>$content,
// 	'msgType'=>$msgType,
// 	'dataDigest'=>$dataDigest,
// 	'sendCode' => $sendCode
// ]);die();

$client = new SoapClient($uri);
$result = $client->receive(
    [
        'content'    => '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',
        'msgType'    => $msgType,
        'dataDigest' => 'aUR+xB5EsqCJ72DMdkquLg+fQ0lludk1QbhxENRRQKxhVJaCPfLFWWeTvotW9ubjr0R3fE//SvP8BjuuAhKH/A==',
        'sendCode'   => $sendCode,
    ]
);

$array = xmlToArray($result->return);
$list  = $array['body']['list'];
var_dump($list);
