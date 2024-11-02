<?php
namespace tools\infrastructure\payment;

use tools\infrastructure\IUser;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\StripeClient;

class StripePayment
{
    protected StripeClient $stripe;
    protected PaymentIntent $paymentIntent;
    protected string $fish;

    public function __construct(string $secretKey){
        $this->stripe = new StripeClient($secretKey);
    }

    public function createPaymentIntent(Customer $customer, float $amount, ?string $onBehalfOf=null, ?string $phone=null):PaymentIntent{
        $option = [];
        if($onBehalfOf !== null){
            $option['on_behalf_of'] = $onBehalfOf;
        }
        return $this->stripe->paymentIntents->create([
            'amount' => $amount * 100,
            'currency' => 'usd',
            'automatic_payment_methods' => [
                'enabled' => true
            ],
            'customer' => $customer->id,
            'description'=> $phone,
            ...$option
        ]);
    }

    public function cancelPaymentIntent(string $paymentIntentId){
        return $this->stripe->paymentIntents->cancel($paymentIntentId, []);
    }

    public function paymentIntent(string $paymentIntentId):PaymentIntent{
        return $this->stripe->paymentIntents->retrieve($paymentIntentId, []);
    }

    public function list(int $limit=20){
        return $this->stripe->paymentIntents->all([
            'limit' => $limit,
        ]);
    }

    public function listByUser(IUser $user, int $limit=10){
        return $this->stripe->paymentIntents->all([
            'limit' => $limit,
            'customer' => $user->id()->toString()
        ]);
    }

    public function searchPaymentIntent(string $query){
        return $this->stripe->paymentIntents->search(['query' => $query]);
    }
}

?>