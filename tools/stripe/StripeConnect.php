<?php
namespace tools\stripe;

use InvalidArgumentException;
use Stripe\Account;
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

    public function createConnect(string $email, string $country): Account {
        try {
            return $this->stripe->accounts->create([
                'type' => 'custom',
                'country' => $country,
                'email' => $email,
                'business_type' => 'individual',
                'requested_capabilities' => [
                    'card_payments',        // Allows the account to accept card payments (credit/debit cards).
                    'transfers',            // Allows the account to receive payouts and transfer funds.
                    'bank_account',         // Allows the account to add and use a bank account for payouts.
                    'payment_methods',      // Allows the account to manage payment methods (add/remove cards, etc.).
                    'identity_verification',// Enables identity verification for the account holder (important for regulatory purposes).
                    'payouts',              // Allows the account to receive payouts from the platform.
                    'charges',              // Allows the account to make charges on behalf of their customers.
                    'subscriptions',        // Allows the account to manage subscriptions (e.g., recurring payments).
                ]
            ]);
        } catch (Throwable $ex) {
            throw new InvalidArgumentException('Failed to create Connect: ' . $ex->getMessage());
        }
    }
}
?>
