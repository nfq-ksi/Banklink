<?php

namespace Banklink\Banklink\EE;

use Banklink\Protocol\iPizza;

/**
 * Banklink implementation for Krediidipank bank using iPizza protocol for communication
 * For specs see http://www.krediidipank.ee/business/settlements/bank-link/tehniline_kirjeldus_inglise.pdf
 *
 * @author Roman Marintsenko <inoryy@gmail.com>
 * @author Markus Karileet <markus.karileet@codehouse.ee>
 * @since  1.11.2012
 */
class Krediidipank extends Banklink
{
    protected $productionRequestUrl = 'https://i-pank.krediidipank.ee/teller/maksa';
    protected $testRequestUrl = 'https://pangalink.net/banklink/krediidipank';

    /**
     * Force iPizza protocol
     *
     * @param iPizza $protocol
     * @param boolean $testMode
     */
    public function __construct(iPizza $protocol, $testMode = false)
    {
        parent::__construct($protocol, $testMode);
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
