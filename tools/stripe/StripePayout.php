<?php
namespace tools\stripe;

use InvalidArgumentException;
use Stripe\Payout;
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

    public function createPayout(float $amount, string $currency, string $connectedAccountId): Payout {
        try {
            return $this->stripe->payouts->create([
                'amount' => $amount * 100,
                'currency' => $currency,
            ], [
                'stripe_account' => $connectedAccountId,
            ]);
    
        } catch (Throwable $ex) {
            throw new InvalidArgumentException('Failed to create Payout: ' . $ex->getMessage());
        }
    }
}
?>
