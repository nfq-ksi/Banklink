<?php

namespace Banklink\Banklink\LT;

use Banklink\Banklink;
use Banklink\Protocol\iPizza;

class DanskeBank extends Banklink
{
    protected $requestUrl = 'https://ebankas.danskebank.lt/ib/site/ibpay/login';
    protected $testRequestUrl = 'https://pangalink.net/banklink/sampo';

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
     * No additional fields
     *
     * @return array
     * @see Banklink::getAdditionalFields()
     *
     */
    protected function getAdditionalFields()
    {
        return [];
    }
}
