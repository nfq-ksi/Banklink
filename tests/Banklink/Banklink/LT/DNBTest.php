<?php

namespace Banklink\Banklink\LT;

use Banklink\Response\PaymentResponse;
use PHPUnit\Framework\TestCase;

class DNBTest extends TestCase
{
    /**
     * @var DNB
     */
    private $dnb;

    public function setUp()
    {
        $protocol = \Mockery::mock('Banklink\Protocol\iPizza')->makePartial();
        $protocol->shouldReceive('getRequestSignature')->once()->andReturn('unit-testing');
        $protocol->shouldReceive('verifyResponseSignature')->once()->andReturn(true);
        $protocol->configure(
            'uid258629',
            'Test Testov',
            '119933113300',
            __DIR__.'/data/iPizza/private_key.pem',
            __DIR__.'/data/iPizza/public_key.pem',
            'http://www.google.com');
        $this->dnb = new DNB($protocol, true);
    }

    public function testPreparePaymentRequest()
    {
        $now = new \DateTime();
        $expectedRequestData = array(
            'VK_SERVICE'  => '1011',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => 'uid258629',
            'VK_STAMP'    => '1',
            'VK_AMOUNT'   => '100',
            'VK_CURR'     => 'EUR',
            'VK_ACC'      => '119933113300',
            'VK_NAME'     => 'Test Testov',
            'VK_REF'      => '13',
            'VK_MSG'      => 'Test payment',
            'VK_RETURN'   => 'http://www.google.com',
            'VK_CANCEL'   => 'http://www.google.com',
            'VK_LANG'     => 'ENG',
            'VK_ENCODING' => 'UTF-8',
            'VK_MAC'      => 'unit-testing',
            'VK_DATETIME' => $now->format(\DateTime::ISO8601)
        );

        $request = $this->dnb->preparePaymentRequest(1, 100, 'Test payment', 'ENG', 'EUR');

        $this->assertEquals($expectedRequestData, $request->getRequestData());
        $this->assertEquals('https://www.dnb.lt/B7-DEMO/dnb-ilinija-demo/', $request->getRequestUrl());
    }

    public function testPreparePaymentRequestWithReferencePrefix()
    {
        $now = new \DateTime();
        $expectedRequestData = array(
            'VK_SERVICE'  => '1011',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => 'uid258629',
            'VK_STAMP'    => '13',
            'VK_AMOUNT'   => '100',
            'VK_CURR'     => 'EUR',
            'VK_ACC'      => '119933113300',
            'VK_NAME'     => 'Test Testov',
            'VK_REF'      => '10100000000000000136',
            'VK_MSG'      => 'Test payment',
            'VK_RETURN'   => 'http://www.google.com',
            'VK_CANCEL'   => 'http://www.google.com',
            'VK_LANG'     => 'ENG',
            'VK_ENCODING' => 'UTF-8',
            'VK_MAC'      => 'unit-testing',
            'VK_DATETIME' => $now->format(\DateTime::ISO8601)
        );

        $this->dnb->setReferencePrefix('101');
        $request = $this->dnb->preparePaymentRequest(13, 100, 'Test payment', 'ENG', 'EUR');

        $this->assertEquals($expectedRequestData, $request->getRequestData());
        $this->assertEquals('https://www.dnb.lt/B7-DEMO/dnb-ilinija-demo/', $request->getRequestUrl());
    }

    public function testHandlePaymentResponseSuccessWithSpecialCharacters()
    {
        $now = new \DateTime();
        $responseData = array(
            'VK_SERVICE'  => '1111',
            'VK_VERSION'  => '008',
            'VK_SND_ID'   => 'GENIPIZZA',
            'VK_REC_ID'   => 'uid258629',
            'VK_STAMP'    => '1',
            'VK_T_NO'     => '18193',
            'VK_AMOUNT'   => '100',
            'VK_CURR'     => 'EUR',
            'VK_REC_ACC'  => '119933113300',
            'VK_REC_NAME' => 'Test Testov',
            'VK_REF'      => '13',
            'VK_MSG'      => 'Test payment',
            'VK_T_DATE'   => '06.11.2012',
            'VK_AUTO'     => 'N',
            'VK_ENCODING' => 'ISO-8859-1',
            'VK_SND_NAME' => mb_convert_encoding('Tõõger Leõpäöld', 'ISO-8859-1', 'UTF-8'),
            'VK_SND_ACC'  => '221234567897',
            'VK_MAC'      => 'unit-testing',
            'VK_T_DATETIME' => $now->format(\DateTime::ISO8601)
        );

        $response = $this->dnb->handleResponse($responseData);

        $this->assertInstanceOf('Banklink\Response\Response', $response);
        $this->assertEquals(PaymentResponse::STATUS_SUCCESS, $response->getStatus());
    }
}
