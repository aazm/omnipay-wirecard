<?php
namespace Omnipay\Wirecard\Message;


use Wirecard\Element\Action\OriginalCredit;
use Wirecard\Element\Job;

class OriginalCreditRequest extends AbstractRequest
{
    protected function buildData()
    {
        $transaction = $this->buildTransaction();
        $originalCredit = new OriginalCredit($transaction);

        return Job::createOriginalCreditJob($this->getSignature(), $originalCredit);
    }
}