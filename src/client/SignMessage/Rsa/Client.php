<?php

namespace customs\CustomsDeclareClient\SignMessage\Rsa;

use customs\CustomsDeclareClient\Application;
use customs\CustomsDeclareClient\Base\BaseClient;

/**
 * 客户端.
 */
class Client extends BaseClient
{
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * 报文RSA加签.
     */
    public function sign($key, $xml)
    {
        $doc               = new \DOMDocument();
        $doc->formatOutput = true;
        $doc->loadXML($xml);
        $objDSig = new XMLSecurityDSig('ds');
        $objDSig->setCanonicalMethod(XMLSecurityDSig::C14N);
        $objDSig->addReference(
            $doc,
            XMLSecurityDSig::SHA1,
            ['http://www.w3.org/2000/09/xmldsig#enveloped-signature'],
            ['force_uri' => true]
        );

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, ['type' => 'private']);
        $objKey->loadKey($key, false);

        $objDSig->sign($objKey, $doc->documentElement);

        return $doc->saveXML();
    }
}
