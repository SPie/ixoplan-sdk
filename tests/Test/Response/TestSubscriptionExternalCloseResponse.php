<?php

namespace Ixolit\Dislo\Test\Response;

use Ixolit\Dislo\Test\WorkingObjects\SubscriptionMock;
use Ixolit\Dislo\WorkingObjects\Subscription;

/**
 * Class TestSubscriptionExternalCloseResponse
 *
 * @package Ixolit\Dislo\Test\Response
 */
class TestSubscriptionExternalCloseResponse implements TestResponseInterface {

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * TestSubscriptionExternalCloseResponse constructor.
     */
    public function __construct() {
        $this->subscription = SubscriptionMock::create();
    }

    /**
     * @return Subscription
     */
    public function getSubscription() {
        return $this->subscription;
    }

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return array
     */
    public function handleRequest($uri, array $data = []) {
        return [
            'subscription' => $this->getSubscription()->toArray(),
        ];
    }

}