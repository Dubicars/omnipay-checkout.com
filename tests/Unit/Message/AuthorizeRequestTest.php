<?php

namespace Omnipay\CheckoutCom\Tests\Unit\Message;

use Omnipay\CheckoutCom\Message\AuthorizeRequest;
use Omnipay\CheckoutCom\Message\AuthorizeResponse;
use Omnipay\Common\Http\Client;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthorizeRequestTest extends TestCase
{

	private $secretKey = 'this-is-the-key';
	private $randomToken = 'random_token';
	private $amount = 400;
	private $currency = 'AED';
	private $successUrl = 'https://successurl.com';
	private $failureUrl = 'https://failureUrl.com';
	private $description = 'this is the description';
	private $paymentType = 'Regular';

	public function setUp(): void
	{
		parent::setUp();
	}

	/**
	 * @throws \Omnipay\CheckoutCom\Exceptions\InvalidAmountException
	 *
	 * @dataProvider getTestData
	 */
	public function testRequestsAreCorrectlySentForDisabled3ds($data)
	{
		$client = $this->createMock(Client::class);

		$request = new Request();
		$authorizeRequest = new AuthorizeRequest($client, $request);
		$response = $this->createMock(ResponseInterface::class);
		$streamResponse = $this->createMock(StreamInterface::class);

		$response->method('getBody')->willReturn($streamResponse);
		$streamResponse->method('getContents')->willReturn(json_encode([]));

		$client->method('request')
			->with('POST', $data['url'], [
				'Authorization' => $this->secretKey,
				'Content-Type' => 'application/json'
			], json_encode($data['params']))
			->willReturn($response);

		$response = $authorizeRequest->initialize($data['paymentOptions'])->send();
		$this->assertInstanceOf(AuthorizeResponse::class, $response);
	}


	public function getTestData()
	{
		return [
			[[//3ds disabled
				'url' => 'https://api.sandbox.checkout.com/payments',
				'paymentOptions' => $this->getPaymentOptions(),
				'params' => $params = $this->getRequestParams()
			]],
			[[//3ds enabled
				'url' => 'https://api.sandbox.checkout.com/payments',
				'paymentOptions' => $this->getPaymentOptions(true),
				'params' => $params = $this->getRequestParams(true)
			]],
			[[//live mode
				'url' => 'https://api.checkout.com/payments',
				'paymentOptions' => $this->getPaymentOptions(true, false),
				'params' => $params = $this->getRequestParams(true)
			]],
		];
	}


	public function getRequestParams($secure = false)
	{
		$params = [
			'source' => [
				'type' => 'token',
				'token' => $this->randomToken,
			],
			'amount' => $this->amount * 100,
			'currency' => $this->currency,
			'success_url' => $this->successUrl,
			'failure_url' => $this->failureUrl,
			'description' => $this->description,
			'payment_type' => $this->paymentType,
		];

		if ($secure) {
			$params['3ds'] = [
				'enabled' => true
			];
		}

		return $params;
	}

	public function getPaymentOptions($secure = false, $testMode = true)
	{
		$options = [
			'testMode' => $testMode,
			'secretKey' => $this->secretKey,
			'amount' => $this->amount,
			'currency' => 'AED',
			'token' => $this->randomToken,
			'returnUrl' => $this->successUrl,
			'cancelUrl' => $this->failureUrl,
			'description' => $this->description
		];
		if ($secure) {
			$options['3ds'] = true;
		}

		return $options;
	}

}
