<?php

namespace Omnipay\Wirecard;

use JMS\Serializer\SerializerBuilder;
use Omnipay\Common\CreditCard;
use Omnipay\Tests\GatewayTestCase;

/**
 * @coversNothing
 */
class GatewayIntegrationTest extends GatewayTestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    protected $paymentOptions;

    protected $referencedOptions;

    protected function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setSerializer($this->getSerializerMock());
        $this->paymentOptions = [
            'card' => new CreditCard([
                'number' => '4200000000000000',
                'expiryYear' => '2019',
                'expiryMonth' => '01',
                'name' => 'John Doe',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'address1' => '550 South Winchester blvd.',
                'address2' => 'P.O. Box 850',
                'country' => 'US',
                'phone' => '+1(202)555-1234',
                'email' => 'John.Doe@email.com',
            ]),
            'amount' => '500.00',
            'currency' => 'EUR',
            'countryCode' => 'DE',
            'transactionId' => '9457892347623478',
        ];
        $this->referencedOptions = [
            'transactionId' => '9457892347623478',
            'transactionReference' => 'C242720181323966504820'
        ];
    }

    protected function getSerializerMock()
    {
        $dir = __DIR__ . '/../vendor/0x4a5k/wirecard-php-api/src/Serializer/Metadata';

        return SerializerBuilder::create()
            ->addMetadataDir($dir, 'Wirecard\Element')
            ->build();
    }

    public function testEnrollmentSuccess()
    {
        $this->setMockHttpResponse('EnrollmentSuccess.txt');
        $response = $this->gateway->enrollmentCheck($this->paymentOptions)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('C242720181323966504820', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testEnrollmentFailure()
    {
        $this->setMockHttpResponse('EnrollmentFailure.txt');
        $response = $this->gateway->enrollmentCheck($this->paymentOptions)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Cardholder not participating.', $response->getMessage());
    }

    public function testPreauthorizationSuccess()
    {
        $this->setMockHttpResponse('PreauthorizationSuccess.txt');
        $response = $this->gateway->preauthorization($this->paymentOptions)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('C898756138191214481743', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testPreauthorizationFailure()
    {
        $this->setMockHttpResponse('PreauthorizationFailure.txt');
        $response = $this->gateway->preauthorization($this->paymentOptions)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Authorization Declined.', $response->getMessage());
    }

    public function testCaptureSuccess()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');
        $response = $this->gateway->capture($this->referencedOptions)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('C305830112714411123351', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testCaptureFailure()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');
        $response = $this->gateway->capture($this->referencedOptions)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('No action taken.', $response->getMessage());
    }

    public function testPurchaseSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->gateway->purchase($this->paymentOptions)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('C242720181323966504820', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testPurchaseFailure()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');
        $response = $this->gateway->purchase($this->paymentOptions)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Authorization Declined.', $response->getMessage());
    }

    public function testReversalSuccess()
    {
        $this->setMockHttpResponse('ReversalSuccess.txt');
        $response = $this->gateway->reversal($this->referencedOptions)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('C242720181323966504820', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testReversalFailure()
    {
        $this->setMockHttpResponse('ReversalFailure.txt');
        $response = $this->gateway->reversal($this->referencedOptions)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('No action taken.', $response->getMessage());
    }

    public function testQuerySuccess()
    {
        $this->setMockHttpResponse('QuerySuccess.txt');
        $response = $this->gateway->query($this->referencedOptions)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('C885511118700326859262', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testQueryFailure()
    {
        $this->setMockHttpResponse('QueryFailure.txt');
        $response = $this->gateway->query($this->referencedOptions)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Authorization Declined.', $response->getMessage());
    }

    public function testBookBackSuccess()
    {
        $this->setMockHttpResponse('BookBackSuccess.txt');
        $response = $this->gateway->bookBack($this->referencedOptions)->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('C242720181323966504820', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }

    public function testBookBackFailure()
    {
        $this->setMockHttpResponse('BookBackFailure.txt');
        $response = $this->gateway->bookBack($this->referencedOptions)->send();
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Expiration date invalid.', $response->getMessage());
    }
}
