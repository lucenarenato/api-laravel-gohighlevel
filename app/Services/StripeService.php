<?php

namespace App\Services;

use Exception;
use Stripe\Subscription;

class StripeService
{
    protected $stripe;

    public function __construct(){
        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));
        // $this->stripe = new \Stripe\StripeClient();
    }

    public function createCustomer($vendor, $token){
        $stripe = new \Stripe\StripeClient(['api_key' => config('services.stripe.secret_key')]);

        $oldCustomer = $stripe->customers->search([
            'query' => 'email:\'' . $vendor['email'] . '\''
        ]);
        
      
        if(count($oldCustomer['data'])) {
            $customer = $oldCustomer['data'][0];
        } else {
            $customer = $stripe->customers->create([
                'name' => $vendor['firstName'] . ' ' . $vendor['lastName'],
                'email' => $vendor['email'],
                'phone' => $vendor['phone'],
                'source' => $token
            ]);
        }


        return $customer;
    }

    public function setupIntents($customerId, $token){
            $stripe = new \Stripe\StripeClient(['api_key' => config('services.stripe.secret_key')]);
            $setupIntents = $stripe->setupIntents->create([
                'customer' => $customerId,
                'payment_method' => $token['card']['id']
            ]);

            return $setupIntents;
    }

    public function getPaymentMethods($customerId){
        $stripe = new \Stripe\StripeClient(['api_key' => config('services.stripe.secret_key')]);
        $paymentMethod = $stripe->paymentMethods->all([
            'type' => 'card',
            'limit' => 1,
            'customer' => $customerId,
        ]);

        return $paymentMethod;
    }

    public function attachPaymentMethod($customerId, $paymentMethodId){
        $stripe = new \Stripe\StripeClient(['api_key' => config('services.stripe.secret_key')]);
        $attachPaymentMethod = $stripe->paymentMethods->attach(
            $paymentMethodId,
            ['customer' => $customerId]
        );
        return $attachPaymentMethod;
    }

    public function createPaymentIntent($amount, $customerId, $cardId){
        $stripe = new \Stripe\StripeClient(['api_key' => config('services.stripe.secret_key')]);
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $amount,
            'automatic_payment_methods' => ['enabled' => true],
            'currency' => 'usd',
            'confirm' => true,
            'customer' => $customerId,
            'setup_future_usage' => 'off_session',
            'statement_descriptor' => 'mymonstro.com',
            'payment_method' => $cardId,
            'return_url' => 'https://mymonstro.com',
        ]);

        return $paymentIntent;
    }

    public function createSubscription($plan, $cycle, $customerId){
        try {
            $subscriptionObject = [
                'customer' => $customerId,
                'description' => "Thanks for subscribing to Monstro. For support email help@mymonstro.com."
            ];
            $subscriptionParams = $this->getSubscriptionParams($plan, $cycle);
            $subscriptionObject = [...$subscriptionObject, ...$subscriptionParams];
            $subscription = Subscription::create($subscriptionObject);
            return $subscription;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    protected function getSubscriptionParams($plan, $cycle)
    {
        $subscriptions = [
            'lite' => [
                'month' => [
                    'items' => [
                        ['price' => 'price_1OSh8vJ7qtdSRbE2ODjgFPwK'],
                    ],
                    // 'coupon' => '3mv0meNa',
                ],
            ],
            'standard' => [
                'month' => [
                    'items' => [
                        ['price' => 'price_1ORZinJ7qtdSRbE2P8ENDrDU'],
                    ],
                    'trial_period_days' => 14,
                ],
            ],
            'scale' => [
                'month' => [
                    'items' => [
                        ['price' => 'price_1OSh9XJ7qtdSRbE2TaSWSGTn'],
                    ],
                    'trial_period_days' => 14,
                ],
                'annual' => [
                    'items' => [
                        ['price' => 'price_1OSh9rJ7qtdSRbE2Zc8AaOcC'],
                    ],
                    'trial_period_days' => 0,
                ],
            ],
            'seo' => [
                'month' => [
                    'items' => [
                        ['price' => 'price_1ORZnhJ7qtdSRbE2tTw1HoHn'],
                        ['price' => 'price_1ORZnHJ7qtdSRbE2yWYiausp'],
                    ],
                    'trial_period_days' => 0,
                ],
            ],
        ];

        return $subscriptions[$plan][$cycle] ?? null;
    }


}