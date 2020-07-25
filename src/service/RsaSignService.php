<?php

namespace customs\CustomsDeclareService;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\Exceptions\ClientError;

/**
 * Rsa加签.
 */
class RsaSignService
{
    /**
     * @ Rsa Client
     */
    private $_rsaClient;

    public function __construct(Application $app)
    {
        $this->_rsaClient = $app['rsa'];
    }

    /**
     * 报文加签.
     *
     * @throws ClientError
     * @throws \Exception
     */
    public function sign(string $key, string $xml)
    {
        if (empty($key) || empty($xml)) {
            throw new ClientError('参数缺失', 1000001);
        }

        return $this->_rsaClient->sign($key, $xml);
    }
}
