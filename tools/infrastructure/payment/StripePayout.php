<?php
namespace tools\infrastructure\payment;

use tools\infrastructure\ParseConfig;

class StripePayout
{
    protected StripePayment $payment;
    protected StripeCustomer $customer;

    public function __construct(){
        $secretKey = ParseConfig::secretKey();
        $this->customer = new StripeCustomer($secretKey);
        $this->payment = new StripePayment($secretKey);
    }

    public function payment():StripePayment{
        return $this->payment;
    }

    public function customer():StripeCustomer{
        return $this->customer;
    }
}

?>