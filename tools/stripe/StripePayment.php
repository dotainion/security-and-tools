<?php
namespace tools\stripe;

use InvalidArgumentException;
use tools\infrastructure\IUser;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Throwable;
use tools\infrastructure\Env;

class StripePayment
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(Env::stripeSecret());
    }

    public function createPaymentIntent(?Customer $customer, string $currency, float $amount, ?string $onBehalfOf = null, ?string $paymentMethodId = null, ?string $destinationId = null, ?float $applicationFee = null): PaymentIntent {
        try {
            $paymentIntentOptions = [
                'amount' => $amount * 100,
                'currency' => $currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
            ];
    
            if ($customer !== null) {
                $paymentIntentOptions['customer'] = $customer->id;
            }
            if ($onBehalfOf !== null) {
                $paymentIntentOptions['on_behalf_of'] = $onBehalfOf;
            }
            if ($paymentMethodId !== null) {
                $paymentIntentOptions['payment_method'] = $paymentMethodId;
                $paymentIntentOptions['confirm'] = true;
            }
            if ($destinationId !== null) {
                $paymentIntentOptions['transfer_data'] = [
                    'destination' => $destinationId
                ];
            }
            if($applicationFee){
                $paymentIntentOptions['application_fee_amount'] = $applicationFee;
            }
        
            return $this->stripe->paymentIntents->create($paymentIntentOptions);
    
        } catch (Throwable $ex) {
            throw new InvalidArgumentException('Failed to create PaymentIntent: ' . $ex->getMessage());
        }
    }

    public function cancelPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return $this->stripe->paymentIntents->cancel($paymentIntentId);
        } catch (Throwable $ex) {
            throw new InvalidArgumentException('Failed to cancel PaymentIntent: ' . $ex->getMessage());
        }
    }

    public function paymentIntent(string $paymentIntentId): PaymentIntent
    {
        try {
            return $this->stripe->paymentIntents->retrieve($paymentIntentId);
        } catch (Throwable $ex) {
            throw new InvalidArgumentException('Failed to retrieve PaymentIntent: ' . $ex->getMessage());
        }
    }

    public function list(int $limit = 20): array{
        try {
            return $this->stripe->paymentIntents->all(['limit' => $limit])->data;
        } catch (Throwable $ex) {
            return [];
        }
    }

    public function listByUser(IUser $user, int $limit = 10): array{
        try {
            return $this->stripe->paymentIntents->all([
                'limit' => $limit,
                'customer' => $user->id()->toString(),
            ])->data;
        } catch (Throwable $ex) {
            return [];
        }
    }

    public function searchPaymentIntent(string $query): array{
        try {
            return $this->stripe->paymentIntents->search(['query' => $query])->data;  // Return an array of PaymentIntents that match the query
        } catch (Throwable $ex) {
            return [];
        }
    }
}
?>
