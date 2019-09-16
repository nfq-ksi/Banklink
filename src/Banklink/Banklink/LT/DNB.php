<?php


namespace Banklink\Banklink\LT;


use Banklink\Banklink;
use Banklink\Protocol\iPizza;

class DNB extends Banklink
{
    protected $requestUrl = 'https://ib.dnb.lt/loginB2B.aspx';
    protected $testRequestUrl = 'https://www.dnb.lt/B7-DEMO/dnb-ilinija-demo/';

    /**
     * Force iPizza protocol
     *
     * @param \Banklink\Protocol\iPizza $protocol
     * @param boolean $testMode
     * @param string | null $requestUrl
     */
    public function __construct(iPizza $protocol, $testMode = false, $requestUrl = null)
    {
        parent::__construct($protocol, $testMode, $requestUrl);
    }

    /**
     * @inheritDoc
     */
    protected function getEncodingField()
    {
        return 'VK_ENCODING';
    }

    /**
     * Add Bank Code
     *
     * @return array
     * @see Banklink::getAdditionalFields()
     *
     */
    protected function getAdditionalFields()
    {
        return [
            'VK_ENCODING' => $this->requestEncoding,
        ];
    }
}
