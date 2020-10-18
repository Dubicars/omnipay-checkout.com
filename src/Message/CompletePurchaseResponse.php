<?php


namespace Omnipay\CheckoutCom\Message;

use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

class CompletePurchaseResponse implements ResponseInterface
{
    private $request;
    private $data;
    private $parameters;
    protected $response;

    public function __construct(CompletePurchaseRequest $request, array $data)
    {
        $this->request = $request;
        $this->response = new ParameterBag($data);
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function isSuccessful()
    {
        return $this->response->has('approved')
            && $this->response->get('approved')=== true
            && ($this->response->get('status') === "Captured" || $this->response->get('status') === "Authorized");
    }

    public function isRedirect()
    {
        return false;
    }

    public function isCancelled()
    {
        // TODO: Implement isCancelled() method.
    }

    public function getMessage()
    {
        $message = '';
        if (isset($this->getData()["response_summary"])) {
            return $this->getData()["response_summary"];
        }

        if (!$this->isSuccessful()) {
            if (isset($this->getData()['actions'])) {
                $actions = $this->getData()['actions'];
                if (isset($actions[0]['response_summary'])) {
                    $message = $actions[0]['response_summary'];
                }
            }
        }

        return $message;
    }

    public function getCode()
    {
        if ($this->isSuccessful()) {
            return $this->getData()["auth_code"] ?? null;
        }

        if (!$this->isSuccessful()) {
            if (isset($this->getData()['actions'])) {
                $actions = $this->getData()['actions'];
                if (isset($actions[0]['response_code'])) {
                    return $actions[0]['response_code'];
                }
            }
        }
    }

    public function getTransactionReference()
    {
        return $this->response->get('id');
    }

    public function getData()
    {
        return $this->response->all();
    }

    public function getStatus()
    {
        return !empty($this->getData()['status']) ? $this->getData()['status']: null;
    }
}
