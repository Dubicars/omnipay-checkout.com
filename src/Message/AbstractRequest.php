<?php

namespace Omnipay\CheckoutCom\Message;

class AbstractRequest
{
    private $liveUrl = "https://api.checkout.com";
    private $sandboxUrl = "https://api.sandbox.checkout.com";
    protected $parameters;

    protected function getUrl($path = '')
    {
        $url = $this->getTestMode() ? $this->sandboxUrl : $this->liveUrl;
        return $url . '/' . $path;
    }

    /**
     * @return mixed
     */
    public function getTestMode()
    {
        return $this->parameters->get('testMode', true);
    }

    /**
     * @param mixed $testMode
     */
    public function setTestMode($testMode)
    {
        $this->parameters->set('testMode', $testMode);
    }
}
