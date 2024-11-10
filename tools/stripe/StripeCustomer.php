<?php
namespace tools\stripe;

use InvalidArgumentException;
use tools\infrastructure\Id;
use Stripe\Customer;
use Stripe\StripeClient;
use Throwable;
use tools\infrastructure\Env;

class StripeCustomer
{
    protected StripeClient $stripe;

    public function __construct(){
        $this->stripe = new StripeClient(Env::stripeSecret());
    }

    public function createCustomerIfNotExist(Id $id, ?string $name, ?string $email, ?string $phone): Customer{
        $customer = $this->customer($id);
        if (!$customer) {
            try {
                $customer = $this->stripe->customers->create([
                    'id' => $id->toString(),
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone
                ]);
            } catch (Throwable $ex) {
                throw new InvalidArgumentException('Failed to create Stripe customer: ' . $ex->getMessage());
            }
        }
        return $customer;
    }

    public function customer(Id $id): ?Customer{
        try {
            return $this->stripe->customers->retrieve($id->toString());
        } catch (Throwable $ex) {
            return null;
        }
    }

    public function list(): array{
        try {
            return $this->stripe->customers->all(['limit' => 3])->data;
        } catch (Throwable $ex) {
            return [];
        }
    }

    public function deleteCustomer(Id $id): bool{
        try {
            return $this->stripe->customers->delete($id->toString())->deleted;
        } catch (Throwable $ex) {
            return false;
        }
    }

    public function searchCustomer(string $query): array{
        try {
            return $this->stripe->customers->search(['query' => $query])->data;
        } catch (Throwable $ex) {
            return [];
        }
    }
}
?>
