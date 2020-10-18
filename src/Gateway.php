<?php

namespace Omnipay\CheckoutCom;

use Omnipay\CheckoutCom\Message\AuthorizeRequest;
use Omnipay\CheckoutCom\Message\CaptureRequest;
use Omnipay\CheckoutCom\Message\RefundRequest;
use Omnipay\CheckoutCom\Message\CompletePurchaseRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\GatewayInterface;

class Gateway extends AbstractGateway implements GatewayInterface
{
    private $secretKey;
    protected $testMode;

    public function getName()
    {
        return 'Checkout Com';
    }

    public function getShortName()
    {
        return 'checkout.com';
    }

    public function getDefaultParameters()
    {
        return [
            'testMode' => true
        ];
    }

    public function initialize(array $parameters = array())
    {
        return parent::initialize($parameters);
    }

    public function authorize(array $options = array())
    {
        $options = array_merge($this->getParameters(), $options);
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    public function capture(array $options = array())
    {
        $options = array_merge($this->getParameters(), $options);
        return $this->createRequest(CaptureRequest::class, $options);
    }

    public function refund(array $options = array())
    {
        $options = array_merge($this->getParameters(), $options);
        return $this->createRequest(RefundRequest::class, $options);
    }

    public function completePurchase(array $options = array())
    {
        $options = array_merge($this->getParameters(), $options);
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    public function setSecretKey($secretKey)
    {
        $this->parameters->set('secretKey', $secretKey);
    }

    public function getSecretKey()
    {
        return $this->parameters->get('secretKey');
    }
}
