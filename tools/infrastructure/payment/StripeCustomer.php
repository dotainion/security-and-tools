<?php
namespace tools\infrastructure\payment;

use tools\infrastructure\IUser;
use tools\module\user\objects\User;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Throwable;

class StripeCustomer
{
    protected StripeClient $stripe;
    protected PaymentIntent $paymentIntent;
    protected string $fish;

    public function __construct(string $secretKey){
        $this->stripe = new StripeClient($secretKey);
    }

    public function createCustomerIfNotExist(User $user):Customer{
        $customer = $this->customer($user);
        if(!$customer){
            $customer = $this->stripe->customers->create([
                'id' => $user->id()->toString(),
                'name' => $user->name(),
                'email' => $user->email(),
                'phone' => $user->phoneNumber()
            ]);
        }
        return $customer;
    }

    public function customer(IUser $user):?Customer{
        try{
            return $this->stripe->customers->retrieve($user->id()->toString(), []);
        }catch(Throwable $ex){
            return null;
        }
    }

    public function list(){
        return $this->stripe->customers->all(['limit' => 3]);
    }

    public function deleteCustomer(IUser $user):Customer{
        return $this->stripe->customers->delete($user->id()->toString(), []);
    }

    public function searchCustomer(string $query):Customer{
        return $this->stripe->customers->delete([
            'query' => $query,
        ]);
    }
}

?>