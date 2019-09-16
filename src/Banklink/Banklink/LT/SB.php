<?php


namespace Banklink\Banklink\LT;


use Banklink\Banklink;
use Banklink\Protocol\iPizza;

class SB extends Banklink
{
    protected $requestUrl = 'https://online.sb.lt/ib/site/ibpay/login';
    protected $testRequestUrl = 'https://pangalink.net/banklink/seb';

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
     * Force UTF-8 encoding
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
