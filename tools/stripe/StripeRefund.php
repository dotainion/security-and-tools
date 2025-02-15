<?php
namespace tools\stripe;

use InvalidArgumentException;
use Stripe\Refund;
use Stripe\StripeClient;
use Throwable;
use tools\infrastructure\Env;

class StripeRefund
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(Env::stripeSecret());
    }

    public function createRefund(string $chargeId, ?float $amount = null): Refund {
        try {
            $charge = [];

            if ($amount !== null) {
                $charge['customer'] = $amount * 100;
            }

            return $this->stripe->refunds->create([
                'charge' => $chargeId,
                ...$charge
            ]);
        } catch (Throwable $ex) {
            throw new InvalidArgumentException('Failed to create Refund: ' . $ex->getMessage());
        }
    }

    public function retrieveRefund(string $refundId): Refund
    {
        try {
            return $this->stripe->refunds->retrieve($refundId);
        } catch (Throwable $ex) {
            throw new InvalidArgumentException('Failed to retrieve Refund: ' . $ex->getMessage());
        }
    }

    public function list(int $limit = 20): array{
        try {
            return $this->stripe->refunds->all(['limit' => $limit])->data;
        } catch (Throwable $ex) {
            return [];
        }
    }
}
?>
