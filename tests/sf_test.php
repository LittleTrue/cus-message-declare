<?php

require_once './vendor/autoload.php';

use customs\CustomsDeclareClient\Application;
use customs\CustomsTradePostFactory;

$ioc_con_app = new Application();

//---------------------------------------------------------
//顺丰运单申报
//---------------------------------------------------------

// 出口电子订单申报
// $declareConfig = [
//     'opt_type'      => 'logistics_declare',
//     'partnerID'     => 'CZA0',
//     'monthly_card'  => '7551234567',
//     'check_word'    => '927xk487ZlaPpcAw6f9o4cT2DpsDDXB2',
//     'timestamp'     => '1600000000'
// ];
    
// $declareParams = [
//     'orderId'   => '2012229135144211',

//     // 寄方
//     'j_contact'       => 'CustomerService',
//     'j_tel'           => '9516168888',
//     'j_country'       => 'CN',
//     'j_province'      => '海南省',
//     'j_city'          => 'US',
//     'j_address'       => '寄方',
//     'j_post_code'     => '98888',

//     // 到方
//     'd_contact'       => '李顺丰',
//     'd_tel'           => '13312908888',
//     'd_country'       => 'CN',
//     'd_post_code'     => '518000',
//     'd_address'       => '到方',

//     'tax_pay_type'    => '1',
//     'express_type'    => '101',
//     'parcel_quantity' => '1',
//     'pay_method'      => '1',
//     'custid'          => '0010002117',
//     'currency'        => 'USD',

//     'Cargo'           => [
//         [
//             'name'              => 'JustaLeafOrganicTea;有机草本茶;芙蓉柠檬2oz(56g)',
//             'count'             => '2',
//             'unit'              => '件',
//             'amount'            => '5.88',
//             'source_area'       => 'USA',
//             'weight'            => '1', // 千克
//             'currency'          => 'USA',
//         ],
//         [
//             'name'              => 'JustaLeafOrganicTea;有机草本茶;芙蓉柠檬2oz(56g)',
//             'count'             => '2',
//             'unit'              => '件',
//             'amount'            => '5.88',
//             'source_area'       => 'USA',
//             'weight'            => '1', // 千克
//             'currency'          => 'USA',
//         ],
//     ],
// ];

// $customsTradePostFactory = new CustomsTradePostFactory();
// $obj                     = $customsTradePostFactory->getInstance('SFLogisticsDeclareService', $ioc_con_app);
// try {
//     $test_xml = $obj->generateFormPost($declareConfig, $declareParams);//print_r($test_xml);die();

//     $r = sendOut(http_build_query($test_xml), 'https://sfapi-sbox.sf-express.com/std/service');
//     var_dump($r);
//     die();
// } catch (\Exception $e) {
//     var_dump($e->getMessage());
// }
// // $test_xml2 = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// // var_dump($test_xml2);
// die();

// ----------------------------------------订单结果查询
// $declareConfig = [
//     'opt_type'      => 'logistics_check',
//     'partnerID'     => 'CZA0',
//     'monthly_card'  => '7551234567',
//     'check_word'    => '927xk487ZlaPpcAw6f9o4cT2DpsDDXB2',
//     'timestamp'     => '1600000000'
// ];
    
// $declareParams = [
//     'orderId'   => '2012229135144211',
// ];

// $customsTradePostFactory = new CustomsTradePostFactory();
// $obj                     = $customsTradePostFactory->getInstance('SFLogisticsDeclareService', $ioc_con_app);
// try {
//     $test_xml = $obj->generateFormPost($declareConfig, $declareParams);//print_r($test_xml);die();
// // var_dump($test_xml);die();
//     $r = sendOut(http_build_query($test_xml), 'https://sfapi-sbox.sf-express.com/std/service');
//     var_dump($r);
//     die();
// } catch (\Exception $e) {
//     var_dump($e->getMessage());
// }
// // $test_xml2 = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// // var_dump($test_xml2);
// die();

// ----------------------------------订单取消
$declareConfig = [
    'opt_type'      => 'logistics_cancel',
    'partnerID'     => 'CZA0',
    'monthly_card'  => '7551234567',
    'check_word'    => '927xk487ZlaPpcAw6f9o4cT2DpsDDXB2',
    'timestamp'     => '1600000000'
];
    
$declareParams = [
    'orderId'   => '2012229135144211',
    'dealType'  => 1
];

$customsTradePostFactory = new CustomsTradePostFactory();
$obj                     = $customsTradePostFactory->getInstance('SFLogisticsDeclareService', $ioc_con_app);
try {
    $test_xml = $obj->generateFormPost($declareConfig, $declareParams);//print_r($test_xml);die();
// var_dump($test_xml);die();
    $r = sendOut(http_build_query($test_xml), 'https://sfapi-sbox.sf-express.com/std/service');
    var_dump($r);
    die();
} catch (\Exception $e) {
    var_dump($e->getMessage());
}
// $test_xml2 = $SummaryBill->generateHttpDoc($declareConfig, $declareParams, $httpBase, $key);

// var_dump($test_xml2);
die();


//发送消息
function sendOut($request_data, $url)
{
    if (empty($request_data)) {
        return ['status' => false, 'message' => '没有请求数据'];
    }
    $curl = new Curl($url, ['time_out' => 4]);
    $headers = [];
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $headers[] = 'Charset: UTF-8';
    $curl->setOpt(CURLOPT_HTTPHEADER, $headers);
    $curl->setPostMethod();
    $curl->addPostData($request_data);
    $curl->send();
    try {
        if (200 != $curl->getStatus()) {
            throw new \Exception('请求' . $url . '发生错误，错误代码:' . $curl->getStatus() . '错误内容:' . $curl->getBody()); //0表示超时
        }
        return ['status' => true, 'message' => $curl->getBody()];
    } catch (\Exception $e) {
        return ['status' => false, 'message' => $e->getMessage()];
    }
}

class Curl
{
    protected $url;

    protected $config = ['time_out' => CURLOPT_TIMEOUT, 'referer' => CURLOPT_REFERER];

    protected $responseInfo = [];

    protected $body;

    protected $ch;

    public function __construct($url, $config = ['time_out' => 30])
    {
        $this->url = $url;
        $this->ch = curl_init($url);
        foreach ($config as $key => $value) {
            if (isset($this->config[$key])) {
                $this->setOpt($this->config[$key], $value);
            }
        }
        $this->init();
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }

    /**
     * curl_setopt — Set an option for a cURL transfer.
     * @param $name
     * @param $value
     */
    public function setOpt($name, $value)
    {
        curl_setopt($this->ch, $name, $value);
    }

    /**
     * curl_setopt_array — Set multiple options for a cURL transfer.
     * @param array $array
     */
    public function setOptArray($array)
    {
        curl_setopt_array($this->ch, $array);
    }

    public function setPostMethod()
    {
        curl_setopt($this->ch, CURLOPT_POST, 1);
    }

    public function addPostData($dataArray)
    {
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $dataArray);
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getStatus()
    {
        return $this->responseInfo['http_code'];
    }

    public function send()
    {
        //this function must be excuted before curl_getinfo();
        $this->body = curl_exec($this->ch);
        $this->responseInfo = curl_getinfo($this->ch);
    }

    public function getInfo()
    {
        return $this->responseInfo;
    }

    protected function init()
    {
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, false);
    }
}
