<?php

namespace Ixolit\Dislo\Response\Billing;


use Ixolit\Dislo\WorkingObjects\Billing\BillingEventObject;


/**
 * Class BillingGetEventResponseObject
 *
 * @package Ixolit\Dislo\Response
 */
final class BillingGetEventResponseObject {

    /**
     * @var BillingEventObject
     */
    private $billingEvent;

    /**
     * @param BillingEventObject $billingEvent
     */
    public function __construct(BillingEventObject $billingEvent) {
        $this->billingEvent = $billingEvent;
    }

    /**
     * @return BillingEventObject
     */
    public function getBillingEvent() {
        return $this->billingEvent;
    }

    /**
     * @param array $response
     *
     * @return BillingGetEventResponseObject
     */
    public static function fromResponse($response) {
        return new self(BillingEventObject::fromResponse($response['billingEvent']));
    }

}