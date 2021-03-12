<?php

namespace Ixolit\Dislo;

use Ixolit\Dislo\Exceptions\AuthenticationException;
use Ixolit\Dislo\Exceptions\AuthenticationInvalidCredentialsException;
use Ixolit\Dislo\Exceptions\AuthenticationRateLimitedException;
use Ixolit\Dislo\Exceptions\DisloException;
use Ixolit\Dislo\Exceptions\InvalidRequestParameterException;
use Ixolit\Dislo\Exceptions\InvalidTokenException;
use Ixolit\Dislo\Exceptions\NotImplementedException;
use Ixolit\Dislo\Exceptions\ObjectNotFoundException;
use Ixolit\Dislo\Response\BillingCloseActiveRecurringResponse;
use Ixolit\Dislo\Response\BillingCloseFlexibleResponse;
use Ixolit\Dislo\Response\BillingCreateFlexibleResponse;
use Ixolit\Dislo\Response\BillingCreatePaymentResponse;
use Ixolit\Dislo\Response\BillingExternalCreateChargebackResponse;
use Ixolit\Dislo\Response\BillingExternalCreateChargeResponse;
use Ixolit\Dislo\Response\BillingExternalGetProfileResponse;
use Ixolit\Dislo\Response\BillingGetActiveRecurringResponse;
use Ixolit\Dislo\Response\BillingGetEventResponse;
use Ixolit\Dislo\Response\BillingGetEventsForUserResponse;
use Ixolit\Dislo\Response\BillingGetFlexibleByIdentifierResponse;
use Ixolit\Dislo\Response\BillingGetFlexibleResponse;
use Ixolit\Dislo\Response\BillingMethodsGetAvailableResponse;
use Ixolit\Dislo\Response\BillingMethodsGetResponse;
use Ixolit\Dislo\Response\CaptchaCreateResponse;
use Ixolit\Dislo\Response\CaptchaVerifyResponse;
use Ixolit\Dislo\Response\CouponCodeCheckResponse;
use Ixolit\Dislo\Response\CouponCodeValidateResponse;
use Ixolit\Dislo\Response\MailTrackOpenedResponse;
use Ixolit\Dislo\Response\MiscGetRedirectorConfigurationResponse;
use Ixolit\Dislo\Response\PackageGetResponse;
use Ixolit\Dislo\Response\PackagesListResponse;
use Ixolit\Dislo\Response\SubscriptionAttachCouponResponse;
use Ixolit\Dislo\Response\SubscriptionCalculateAddonPriceResponse;
use Ixolit\Dislo\Response\SubscriptionCalculatePackageChangeResponse;
use Ixolit\Dislo\Response\SubscriptionCalculatePriceResponse;
use Ixolit\Dislo\Response\SubscriptionCallSpiResponse;
use Ixolit\Dislo\Response\SubscriptionCancelPackageChangeResponse;
use Ixolit\Dislo\Response\SubscriptionCancelResponse;
use Ixolit\Dislo\Response\SubscriptionChangeResponse;
use Ixolit\Dislo\Response\SubscriptionCloseResponse;
use Ixolit\Dislo\Response\SubscriptionContinueResponse;
use Ixolit\Dislo\Response\SubscriptionCreateAddonResponse;
use Ixolit\Dislo\Response\SubscriptionCreateResponse;
use Ixolit\Dislo\Response\SubscriptionExternalAddonCreateResponse;
use Ixolit\Dislo\Response\SubscriptionExternalChangePeriodResponse;
use Ixolit\Dislo\Response\SubscriptionExternalChangeResponse;
use Ixolit\Dislo\Response\SubscriptionExternalCloseResponse;
use Ixolit\Dislo\Response\SubscriptionExternalCreateResponse;
use Ixolit\Dislo\Response\SubscriptionFireEventResponse;
use Ixolit\Dislo\Response\SubscriptionGetAllResponse;
use Ixolit\Dislo\Response\SubscriptionGetMetadataElementsResponse;
use Ixolit\Dislo\Response\SubscriptionGetPeriodEventsResponse;
use Ixolit\Dislo\Response\SubscriptionGetPossiblePlanChangeStrategiesResponse;
use Ixolit\Dislo\Response\SubscriptionGetPossibleUpgradesResponse;
use Ixolit\Dislo\Response\SubscriptionGetResponse;
use Ixolit\Dislo\Response\SubscriptionMetadataChangeResponse;
use Ixolit\Dislo\Response\SubscriptionValidateMetaDataResponse;
use Ixolit\Dislo\Response\UserAuthenticateResponse;
use Ixolit\Dislo\Response\UserChangeResponse;
use Ixolit\Dislo\Response\UserCreateResponse;
use Ixolit\Dislo\Response\UserDeauthenticateResponse;
use Ixolit\Dislo\Response\UserDeleteResponse;
use Ixolit\Dislo\Response\UserDisableLoginResponse;
use Ixolit\Dislo\Response\UserEmailVerificationFinishResponse;
use Ixolit\Dislo\Response\UserEmailVerificationStartResponse;
use Ixolit\Dislo\Response\UserEnableLoginResponse;
use Ixolit\Dislo\Response\UserFindResponse;
use Ixolit\Dislo\Response\UserFireEventResponse;
use Ixolit\Dislo\Response\UserGetAuthenticatedResponse;
use Ixolit\Dislo\Response\UserGetBalanceResponse;
use Ixolit\Dislo\Response\UserGetMetaProfileResponse;
use Ixolit\Dislo\Response\UserGetResponse;
use Ixolit\Dislo\Response\UserGetTokensResponse;
use Ixolit\Dislo\Response\UserPhoneVerificationFinishResponse;
use Ixolit\Dislo\Response\UserPhoneVerificationStartResponse;
use Ixolit\Dislo\Response\UserRecoveryCheckResponse;
use Ixolit\Dislo\Response\UserRecoveryFinishResponse;
use Ixolit\Dislo\Response\UserRecoveryStartResponse;
use Ixolit\Dislo\Response\UserSmsVerificationFinishResponse;
use Ixolit\Dislo\Response\UserSmsVerificationStartResponse;
use Ixolit\Dislo\Response\UserUpdateTokenResponse;
use Ixolit\Dislo\Response\UserValidateMetaDataResponse;
use Ixolit\Dislo\Response\UserVerificationStartResponse;
use Ixolit\Dislo\WorkingObjects\BillingEvent;
use Ixolit\Dislo\WorkingObjects\Flexible;
use Ixolit\Dislo\WorkingObjects\Subscription;
use Ixolit\Dislo\WorkingObjects\User;
use Psr\Http\Message\StreamInterface;

/**
 * The main client class for use with the IXOPLAN API.
 *
 * Designed for different transport layers. Requires a RequestClient interface for actual communication with IXOPLAN
 * (e.g. HTTPRequestClient).
 *
 * For details about the IXOPLAN API, the available calls and IXOPLAN itself please read the documentation available at
 * https://docs.ixoplan.com/
 */
class Client extends AbstractClient {

    const API_URI_BILLING_METHODS_GET                            = '/frontend/billing/getPaymentMethods';
    const API_URI_BILLING_METHODS_GET_FOR_PACKAGE                = '/frontend/billing/getPaymentMethodsForPackage';
    const API_URI_BILLING_CLOSE_FLEXIBLE                         = '/frontend/billing/closeFlexible';
    const API_URI_BILLING_CREATE_FLEXIBLE                        = '/frontend/billing/createFlexible';
    const API_URI_BILLING_CREATE_PAYMENT                         = '/frontend/billing/createPayment';
    const API_URI_BILLING_EXTERNAL_CREATE_CHARGE                 = '/frontend/billing/externalCreateCharge';
    const API_URI_BILLING_EXTERNAL_CREATE_CHARGE_WITHOUT_PROFILE = '/frontend/billing/externalCreateChargeWithoutProfile';
    const API_URI_BILLING_EXTERNAL_CREATE_CHARGEBACK             = '/frontend/billing/externalCreateChargeback';
    const API_URI_BILLING_EXTERNAL_GET_PROFILE                   = '/frontend/billing/externalGetProfile';
    const API_URI_BILLING_GET_EVENT                              = '/frontend/billing/getBillingEvent';
    const API_URI_BILLING_GET_EVENTS_FOR_USER                    = '/frontend/billing/getBillingEventsForUser';
    const API_URI_BILLING_GET_FLEXIBLE                           = '/frontend/billing/getFlexible';
    const API_URI_BILLING_GET_FLEXIBLE_BY_ID                     = '/frontend/billing/getFlexibleById';
    const API_URI_BILLING_GET_ACTIVE_RECURRING                   = '/frontend/billing/getActiveRecurring';
    const API_URI_BILLING_CLOSE_ACTIVE_RECURRING                 = '/frontend/billing/closeActiveRecurring';

    const API_URI_SUBSCRIPTION_CALCULATE_ADDON_PRICE              = '/frontend/subscription/calculateAddonPrice';
    const API_URI_SUBSCRIPTION_CALCULATE_PACKAGE_CHANGE           = '/frontend/subscription/calculatePackageChange';
    const API_URI_SUBSCRIPTION_CALCULATE_PRICE                    = '/frontend/subscription/calculateSubscriptionPrice';
    const API_URI_SUBSCRIPTION_CANCEL_PACKAGE_CHANGE              = '/frontend/subscription/cancelPackageChange';
    const API_URI_SUBSCRIPTION_CANCEL                             = '/frontend/subscription/cancel';
    const API_URI_SUBSCRIPTION_CHANGE                             = '/frontend/subscription/changePackage';
    const API_URI_COUPON_CODE_CHECK                               = '/frontend/subscription/checkCouponCode';
    const API_URI_SUBSCRIPTION_CLOSE                              = '/frontend/subscription/close';
    const API_URI_SUBSCRIPTION_CONTINUE                           = '/frontend/subscription/continue';
    const API_URI_SUBSCRIPTION_CREATE_ADDON                       = '/frontend/subscription/createAddonSubscription';
    const API_URI_SUBSCRIPTION_CREATE                             = '/frontend/subscription/create';
    const API_URI_SUBSCRIPTION_EXTERNAL_CHANGE                    = '/frontend/subscription/externalChangePackage';
    const API_URI_SUBSCRIPTION_EXTERNAL_CHANGE_PERIOD             = '/frontend/subscription/externalChangePeriod';
    const API_URI_SUBSCRIPTION_EXTERNAL_CLOSE                     = '/frontend/subscription/externalCloseSubscription';
    const API_URI_SUBSCRIPTION_EXTERNAL_CREATE_ADDON_SUBSCRIPTION = '/frontend/subscription/externalCreateAddonSubscription';
    const API_URI_SUBSCRIPTION_EXTERNAL_CREATE                    = '/frontend/subscription/externalCreateSubscription';
    const API_URI_SUBSCRIPTION_CALL_SPI                           = '/frontend/subscription/callSpi';
    const API_URI_SUBSCRIPTION_GET_POSSIBLE_PLAN_CHANGES          = '/frontend/subscription/getPossiblePlanChanges';
    const API_URI_SUBSCRIPTION_GET_POSSIBLE_PLAN_CHANGE_STRATEGIES= '/frontend/subscription/getPossiblePlanChangeStrategies';
    const API_URI_PACKAGE_LIST                                    = '/frontend/subscription/getPackages';
    const API_URI_SUBSCRIPTION_GET                                = '/frontend/subscription/get';
    const API_URI_SUBSCRIPTION_GET_ALL                            = '/frontend/subscription/getSubscriptions';
    const API_URI_SUBSCRIPTION_GET_PERIOD_EVENTS                  = '/frontend/subscription/getPeriodHistory';
    const API_URI_SUBSCRIPTION_ATTACH_COUPON                      = '/frontend/subscription/attachCoupon';
    const API_URI_SUBSCRIPTION_FIRE_EVENT                         = '/frontend/subscription/fireEvent';
    const API_URI_COUPON_CODE_VALIDATE                            = '/frontend/subscription/validateCoupon';

    const API_URI_USER_AUTHENTICATE        = '/frontend/user/authenticate';
    const API_URI_USER_DEAUTHENTICATE      = '/frontend/user/deAuthToken';
    const API_URI_USER_CHANGE              = '/frontend/user/change';
    const API_URI_USER_CHANGE_PASSWORD     = '/frontend/user/changePassword';
    const API_URI_USER_CREATE              = '/frontend/user/create';
    const API_URI_USER_DELETE              = '/frontend/user/delete';
    const API_URI_USER_DISABLE_LOGIN       = '/frontend/user/disableLogin';
    const API_URI_USER_ENABLE_LOGIN        = '/frontend/user/enableLogin';
    const API_URI_USER_GET_ACCOUNT_BALANCE = '/frontend/user/getBalance';
    const API_URI_USER_GET_META_PROFILE    = '/frontend/user/getMetaProfile';
    const API_URI_USER_GET_AUTH_TOKENS     = '/frontend/user/getTokens';
    const API_URI_USER_GET                 = '/frontend/user/get';
    const API_URI_USER_UPDATE_AUTH_TOKEN   = '/frontend/user/updateToken';
    const API_URI_USER_EXTEND_AUTH_TOKEN   = '/frontend/user/extendTokenLifeTime';
    const API_URI_USER_GET_AUTHENTICATED   = '/frontend/user/getAuthenticated';
    const API_URI_USER_FIND                = '/frontend/user/findUser';
    const API_URI_USER_RECOVERY_START      = '/frontend/user/passwordRecovery/start';
    const API_URI_USER_RECOVERY_CHECK      = '/frontend/user/passwordRecovery/check';
    const API_URI_USER_RECOVERY_FINISH     = '/frontend/user/passwordRecovery/finalize';
    const API_URI_USER_VERIFICATION_START  = '/frontend/user/verification/start';
    const API_URI_USER_VERIFICATION_FINISH = '/frontend/user/verification/finalize';
    const API_URI_USER_FIRE_EVENT          = '/frontend/user/fireEvent';

    const API_URI_TRACK_OPENED_MAIL            = '/frontend/misc/trackOpenedMail';
    const API_URI_REDIRECTOR_GET_CONFIGURATION = '/frontend/misc/getRedirectorConfiguration';
    const API_URI_EXPORT_STREAM_REPORT         = '/export/v2/report/';
    const API_URI_EXPORT_STREAM_QUERY          = '/export/v2/query';

    const API_URI_SYSTEM_INFO               = '/system/info/get';

    const COUPON_EVENT_START   = 'subscription_start';
    const COUPON_EVENT_UPGRADE = 'subscription_upgrade';

    const PLAN_CHANGE_IMMEDIATE = 'immediate';
    const PLAN_CHANGE_QUEUED    = 'queued';

    /**
     * Retrieve the list of payment methods.
     *
     * @param string|null $packageIdentifier
     * @param string|null $countryCode
     * @param string|null $currencyCode
     * @param bool        $isAvailable
     *
     * @return BillingMethodsGetResponse
     */
	public function billingMethodsGet(
        $packageIdentifier = null,
        $countryCode = null,
        $currencyCode = null,
        $isAvailable = false
	) {
	    if ($packageIdentifier) {
            return BillingMethodsGetResponse::fromResponse(
                $this->request(
	    		    self::API_URI_BILLING_METHODS_GET_FOR_PACKAGE,
                    [
                        'packageIdentifier' => $packageIdentifier,
                        'countryCode'       => $countryCode,
                        'currencyCode'      => $currencyCode,
                    ]
                )
            );
        }

		return BillingMethodsGetResponse::fromResponse(
    		 $this->request(
                self::API_URI_BILLING_METHODS_GET,
                [
                    'isAvailable'  => $isAvailable,
                    'countryCode'  => $countryCode,
                    'currencyCode' => $currencyCode,
                ]
            )
        );
	}

    /**
     * Retrieve the list of available payment methods.
     *
     * @param string|null $packageIdentifier
     * @param string|null $countryCode
     * @param string|null $currencyCode
     *
     * @return BillingMethodsGetAvailableResponse
     */
	public function billingMethodsGetAvailable($packageIdentifier = null, $countryCode = null, $currencyCode = null)
    {
		$billingMethods = $this->billingMethodsGet($packageIdentifier, $countryCode, $currencyCode, true)
            ->getBillingMethods();

		return new BillingMethodsGetAvailableResponse($billingMethods);
	}

	/**
	 * Closes the flexible payment method for a user.
	 *
	 * Note: once you close an active flexible, subscriptions cannot get extended automatically!
	 *
	 * @param Flexible|int    $flexible
	 * @param User|int|string $userTokenOrId User authentication token or user ID.
	 *
	 * @return BillingCloseFlexibleResponse
	 *
	 * @throws DisloException
	 */
	public function billingCloseFlexible(
		$flexible,
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$data['flexibleId'] = ($flexible instanceof Flexible ? $flexible->getFlexibleId() : (int)$flexible);

		$response = $this->request(self::API_URI_BILLING_CLOSE_FLEXIBLE, $data);
		return BillingCloseFlexibleResponse::fromResponse($response);
	}

    /**
     * Create a new flexible for a user.
     *
     * Note: there can only be ONE active flexible per user. In case there is already an active one, and you
     * successfully create a new one, the old flexible will be closed automatically.
     *
     * @param User|int|string $userTokenOrId User authentication token or user ID.
     * @param string          $billingMethod
     * @param string          $returnUrl
     * @param array           $paymentDetails
     * @param string          $currencyCode
     *
     * @param null            $subscriptionId
     *
     * @return BillingCreateFlexibleResponse
     */
    public function billingCreateFlexible(
        $userTokenOrId,
        $billingMethod,
        $returnUrl,
        $paymentDetails,
        $currencyCode = '',
        $subscriptionId = null
    ) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$data['billingMethod']  = $billingMethod;
		$data['returnUrl']      = (string)$returnUrl;
		$data['paymentDetails'] = $paymentDetails;
		$data['subscriptionId'] = $subscriptionId;
		if ($currencyCode) {
			$data['currencyCode'] = $currencyCode;
		}
		$response = $this->request(self::API_URI_BILLING_CREATE_FLEXIBLE, $data);
		return BillingCreateFlexibleResponse::fromResponse($response);
	}

	/**
	 * Initiate a payment transaction for a new subscription or package change.
	 *
	 * Only use CreatePayment if you want to create an actual payment for a subscription that needs billing. If you
	 * try to create a payment for a subscription that doesn't need one, you will receive the error No subscription
	 * or upgrade found for payment. If you just want to register a payment method, use `billingCreateFlexible()`
	 * instead.
	 *
	 * @param Subscription|int $subscription
	 * @param string           $billingMethod
	 * @param string           $returnUrl
	 * @param array            $paymentDetails
	 * @param User|int|string  $userTokenOrId user authentication token or id
	 * @param string|null      $countryCode
	 *
	 * @return BillingCreatePaymentResponse
	 */
	public function billingCreatePayment(
		$subscription,
		$billingMethod,
		$returnUrl,
		$paymentDetails,
		$userTokenOrId,
		$countryCode = null
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$data['billingMethod']  = $billingMethod;
		$data['returnUrl']      = (string)$returnUrl;
		$data['subscriptionId'] =
			($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription);
		$data['paymentDetails'] = $paymentDetails;
		$data['countryCode'] = $countryCode;
		$response               = $this->request(self::API_URI_BILLING_CREATE_PAYMENT, $data);
		if (empty($response['redirectUrl'])) {
			$response['redirectUrl'] = $returnUrl;
		}
		return BillingCreatePaymentResponse::fromResponse($response);
	}

	/**
	 * Create an external charge.
	 *
	 * @param string          $externalProfileId     the external profile to which the charge should be linked, this is
	 *                                               the "externalId" you passed in the "subscription/externalCreate"
	 *                                               call
	 * @param string          $accountIdentifier     the billing account identifier, you will this from IXOPLAN staff
	 * @param string          $currencyCode          currency code EUR, USD, ...
	 * @param float           $amount                the amount of the charge
	 * @param string          $externalTransactionId external unique id for the charge
	 * @param int|null        $upgradeId             the unique upgrade id to which the charge should be linked, you
	 *                                               get this from the "subscription/externalChangePackage" or
	 *                                               "subscription/externalCreateAddonSubscription" call
	 * @param array           $paymentDetails        additional data you want to save with the charge
	 * @param string          $description           description of the charge
	 * @param string          $status                status the charge should be created with, you might want to log
	 *                                               erroneous charges in IXOPLAN too, but you don't have to. @see
	 *                                               BillingEvent::STATUS_*
	 * @param User|int|string $userTokenOrId         User authentication token or user ID.
	 *
	 * @return BillingExternalCreateChargeResponse
	 *
	 * @throws DisloException
	 */
	public function billingExternalCreateCharge(
		$externalProfileId,
		$accountIdentifier,
		$currencyCode,
		$amount,
		$externalTransactionId,
		$upgradeId = null,
		$paymentDetails = [],
		$description = '',
		$status = BillingEvent::STATUS_SUCCESS,
		$userTokenOrId = null
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$data['externalProfileId']     = $externalProfileId;
		$data['accountIdentifier']     = $accountIdentifier;
		$data['currencyCode']          = $currencyCode;
		$data['amount']                = $amount;
		$data['externalTransactionId'] = $externalTransactionId;
		$data['upgradeId']             = $upgradeId;
		$data['paymentDetails']        = $paymentDetails;
		$data['description']           = $description;
		$data['status']                = $status;

		$response = $this->request(self::API_URI_BILLING_EXTERNAL_CREATE_CHARGE, $data);
		return BillingExternalCreateChargeResponse::fromResponse($response);
	}

	/**
	 * Create an external charge without an external profile.
	 *
	 * @param string          $accountIdentifier     the billing account identifier, you will this from IXOPLAN staff
	 * @param string          $currencyCode          currency code EUR, USD, ...
	 * @param float           $amount                the amount of the charge
	 * @param string          $externalTransactionId external unique id for the charge
	 * @param array           $paymentDetails        additional data you want to save with the charge
	 * @param string          $description           description of the charge
	 * @param string          $status                status the charge should be created with, you might want to log
	 *                                               erroneous charges in IXOPLAN too, but you don't have to. @see
	 *                                               BillingEvent::STATUS_*
	 * @param User|int|string $userTokenOrId         User authentication token or user ID.
	 *
	 * @return BillingExternalCreateChargeResponse
	 *
	 * @throws DisloException
	 */
	public function billingExternalCreateChargeWithoutProfile(
		$accountIdentifier,
		$currencyCode,
		$amount,
		$externalTransactionId = null,
		$paymentDetails = [],
		$description = '',
		$status = BillingEvent::STATUS_SUCCESS,
		$userTokenOrId = null
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$data['accountIdentifier']     = $accountIdentifier;
		$data['currencyCode']          = $currencyCode;
		$data['amount']                = $amount;
		$data['externalTransactionId'] = $externalTransactionId;
		$data['paymentDetails']        = $paymentDetails;
		$data['description']           = $description;
		$data['status']                = $status;

		$response = $this->request(self::API_URI_BILLING_EXTERNAL_CREATE_CHARGE_WITHOUT_PROFILE, $data);
		return BillingExternalCreateChargeResponse::fromResponse($response);
	}

	/**
	 * Create a charge back for an external charge by using the original transaction ID
	 *
	 * @param string          $accountIdentifier     the billing account identifier, assigned by IXOPLAN staff
	 * @param string          $originalTransactionID external unique id of the original charge
	 * @param string          $description           textual description of the chargeback for support
	 * @param User|int|string $userTokenOrId         User authentication token or user ID.
	 *
	 * @return BillingExternalCreateChargebackResponse
	 *
	 * @throws DisloException
	 */
	public function billingExternalCreateChargebackByTransactionId(
		$accountIdentifier,
		$originalTransactionID,
		$description = '',
		$userTokenOrId = null
	) {
		$data = [
			'accountIdentifier'     => $accountIdentifier,
			'externalTransactionId' => $originalTransactionID,
			'description'           => $description,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_EXTERNAL_CREATE_CHARGEBACK, $data);
		return BillingExternalCreateChargebackResponse::fromResponse($response);
	}

	/**
	 * Create a charge back for an external charge by using the original billing event ID
	 *
	 * @param string          $accountIdentifier      the billing account identifier, assigned by IXOPLAN staff
	 * @param int             $originalBillingEventId ID of the original billing event.
	 * @param string          $description            textual description of the chargeback for support
	 * @param User|int|string $userTokenOrId          User authentication token or user ID.
	 *
	 * @return BillingExternalCreateChargebackResponse
	 *
	 * @throws DisloException
	 */
	public function billingExternalCreateChargebackByEventId(
		$accountIdentifier,
		$originalBillingEventId,
		$description = '',
		$userTokenOrId = null
	) {
		$data = [
			'accountIdentifier' => $accountIdentifier,
			'billingEventId'    => $originalBillingEventId,
			'description'       => $description,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_EXTERNAL_CREATE_CHARGEBACK, $data);
		return BillingExternalCreateChargebackResponse::fromResponse($response);
	}

	/**
	 * Retrieve an external profile by the external id that has been passed in "subscription/externalCreate".
	 *
	 * @param string          $externalId    ID for the external profile
	 * @param User|int|string $userTokenOrId User authentication token or user ID.
	 *
	 * @return BillingExternalGetProfileResponse
	 *
	 * @throws DisloException
	 */
	public function billingExternalGetProfileByExternalId(
		$externalId,
		$userTokenOrId = null
	) {
		$data = [
			'externalId' => $externalId,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_EXTERNAL_GET_PROFILE, $data);
		return BillingExternalGetProfileResponse::fromResponse($response);
	}

	/**
	 * Retrieve an external profile by the external id that has been passed in "subscription/externalCreate".
	 *
	 * @param Subscription|int $subscription  ID for the subscription expected to have an external profile
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return BillingExternalGetProfileResponse
	 *
	 * @throws DisloException
	 */
	public function billingExternalGetProfileBySubscriptionId(
		$subscription,
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_EXTERNAL_GET_PROFILE, $data);
		return BillingExternalGetProfileResponse::fromResponse($response);
	}

	/**
	 * Create a charge back for an external charge by using the original billing event ID.
	 *
	 * @param int             $billingEventId unique id of the billing event
	 * @param User|int|string $userTokenOrId  User authentication token or user ID.
	 *
	 * @return BillingGetEventResponse
	 *
	 * @throws DisloException
	 */
	public function billingGetEvent(
		$billingEventId,
		$userTokenOrId = null
	) {
		$data     = [
			'billingEventId' => $billingEventId,
		];
		$data     = $this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_GET_EVENT, $data);
		return BillingGetEventResponse::fromResponse($response);
	}

    /**
     * Create a charge back for an external charge by using the original billing event ID.
     *
     * @param User|int|string $userTokenOrId User authentication token or user ID.
     * @param int             $limit
     * @param int             $offset
     * @param string          $orderDir
     * @param array           $eventTypeWhitelist
     *
     * @return BillingGetEventsForUserResponse
     *
     * @throws DisloException
     * @throws ObjectNotFoundException
     */
	public function billingGetEventsForUser(
		$userTokenOrId,
		$limit = 10,
		$offset = 0,
		$orderDir = self::ORDER_DIR_ASC,
        $eventTypeWhitelist = []
	) {
		$data = [
            'limit'              => $limit,
            'offset'             => $offset,
            'orderDir'           => $orderDir,
            'eventTypeWhitelist' => $eventTypeWhitelist,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_GET_EVENTS_FOR_USER, $data);
		return BillingGetEventsForUserResponse::fromResponse($response);
	}

	/**
	 * Get flexible payment method for a user
	 *
	 * @param User|int|string $userTokenOrId User authentication token or user ID.
	 *
	 * @return BillingGetFlexibleResponse
	 *
	 * @throws DisloException
	 */
	public function billingGetFlexible(
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_GET_FLEXIBLE, $data);
		return BillingGetFlexibleResponse::fromResponse($response);
	}

	/**
	 * Get specific payment method for a user by its identifier
	 *
	 * @param int 			  $flexibleIdentifier
	 * @param User|int|string $userTokenOrId User authentication token or user ID.
	 *
	 * @return BillingGetFlexibleByIdentifierResponse
	 *
	 * @throws DisloException
	 * @throws ObjectNotFoundException
	 */
	public function billingGetFlexibleByIdentifier(
		$flexibleIdentifier,
		$userTokenOrId
	) {
		$data = [
			'flexibleId' => $flexibleIdentifier
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_GET_FLEXIBLE_BY_ID, $data);
		return BillingGetFlexibleByIdentifierResponse::fromResponse($response);
	}

	/**
	 * Get active recurring payment method for a subscription
	 *
	 * @param Subscription|int $subscription  ID for the subscription expected to have an external profile
	 * @param User|int|string $userTokenOrId User authentication token or user ID.
	 *
	 * @return BillingGetActiveRecurringResponse
	 *
	 * @throws DisloException
	 */
	public function billingGetActiveRecurring(
		$subscription,
		$userTokenOrId
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_GET_ACTIVE_RECURRING, $data);
		return BillingGetActiveRecurringResponse::fromResponse($response);
	}

	/**
	 * Close active recurring payment method for a subscription
	 *
	 * @param Subscription|int $subscription  ID for the subscription expected to have an external profile
	 * @param User|int|string $userTokenOrId User authentication token or user ID.
	 *
	 * @return BillingCloseActiveRecurringResponse
	 *
	 * @throws DisloException
	 */
	public function billingCloseActiveRecurring(
		$subscription,
		$userTokenOrId
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_BILLING_CLOSE_ACTIVE_RECURRING, $data);
		return BillingCloseActiveRecurringResponse::fromResponse($response);
	}

	/**
	 * Calculate the price for a subscription addon.
	 *
	 * @param Subscription|int $subscription
	 * @param string|string[]  $packageIdentifiers
	 * @param string|null      $couponCode
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 * @param string|null      $strategyIdentifier
	 *
	 * @return SubscriptionCalculateAddonPriceResponse
	 *
	 * @throws DisloException
	 */
	public function subscriptionCalculateAddonPrice(
		$subscription,
		$packageIdentifiers,
		$couponCode = null,
		$userTokenOrId,
		$strategyIdentifier = null
	) {
		$data = [
			'subscriptionId'     =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'packageIdentifiers' => $packageIdentifiers,
			'couponCode'         => $couponCode,
		];
		if ($strategyIdentifier) {
			$data['strategyIdentifier'] = $strategyIdentifier;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CALCULATE_ADDON_PRICE, $data);
		return SubscriptionCalculateAddonPriceResponse::fromResponse($response);
	}

	/**
	 * Calculate the price for a potential package change.
	 *
	 * @param Subscription|int $subscription
	 * @param string           $newPackageIdentifier
	 * @param string|null      $couponCode
	 * @param User|string|int  $userTokenOrId User authentication token or user ID.
	 * @param string[]         $addonPackageIdentifiers
	 * @param string|null      $strategyIdentifier
	 *
	 * @return SubscriptionCalculatePackageChangeResponse
	 */
	public function subscriptionCalculatePackageChange(
		$subscription,
		$newPackageIdentifier,
		$couponCode = null,
		$userTokenOrId = null,
		$addonPackageIdentifiers = [],
		$strategyIdentifier = null
	) {
		$data = [
			'subscriptionId'          =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'newPackageIdentifier'    => $newPackageIdentifier,
			'couponCode'           	  => $couponCode,
			'addonPackageIdentifiers' => $addonPackageIdentifiers
		];
		if ($strategyIdentifier) {
			$data['strategyIdentifier'] = $strategyIdentifier;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CALCULATE_PACKAGE_CHANGE, $data);
		return SubscriptionCalculatePackageChangeResponse::fromResponse($response);
	}

	/**
	 * Calculates the price for creating a new subscription for an existing user.
	 *
	 * @param string          $packageIdentifier       the package for the subscription
	 * @param string          $currencyCode            currency which should be used for the user
	 * @param string|null     $couponCode              optional - coupon which should be applied
	 * @param string|string[] $addonPackageIdentifiers optional - additional addon packages
	 * @param User|int|string $userTokenOrId           User authentication token or user ID.
	 *
	 * @return SubscriptionCalculatePriceResponse
	 */
	public function subscriptionCalculatePrice(
		$packageIdentifier,
		$currencyCode,
		$couponCode = null,
		$addonPackageIdentifiers = [],
		$userTokenOrId
	) {
		$data = [
			'packageIdentifier'       => $packageIdentifier,
			'currencyCode'            => $currencyCode,
			'couponCode'              => $couponCode,
			'addonPackageIdentifiers' => $addonPackageIdentifiers,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CALCULATE_PRICE, $data);
		return SubscriptionCalculatePriceResponse::fromResponse($response);
	}

	/**
	 * Cancel a future package change
	 *
	 * NOTE: this call only works for package changes which are not applied immediately. In that case you need to call
	 * ChangePackage again.
	 *
	 * @param Subscription|int $subscription  the unique subscription id to change
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return SubscriptionCancelPackageChangeResponse
	 */
	public function subscriptionCancelPackageChange(
		$subscription,
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CANCEL_PACKAGE_CHANGE, $data);
		return SubscriptionCancelPackageChangeResponse::fromResponse($response);
	}

	/**
	 * Cancels a single subscription.
	 *
	 * @param Subscription|int $subscription         the id of the subscription you want to cancel
	 * @param string           $cancelReason         optional - the reason why the user canceled (should be predefined
	 *                                               reasons by your frontend)
	 * @param string           $userCancelReason     optional - a user defined cancellation reason
	 * @param string           $userComments         optional - comments from the user
	 * @param User|int|string  $userTokenOrId        User authentication token or user ID.
	 *
	 * @return SubscriptionCancelResponse
	 */
	public function subscriptionCancel(
		$subscription,
		$cancelReason = '',
		$userCancelReason = '',
		$userComments = '',
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId'   =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'cancelReason'     => $cancelReason,
			'userCancelReason' => $userCancelReason,
			'userComments'     => $userComments,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CANCEL, $data);
		return SubscriptionCancelResponse::fromResponse($response);
	}

	/**
	 * Change the package for a subscription.
	 *
	 * @param Subscription|int $subscription                the unique subscription id to change
	 * @param string           $newPackageIdentifier        the identifier of the new package
	 * @param string[]         $addonPackageIdentifiers     optional - package identifiers of the addons
	 * @param string           $couponCode                  optional - the coupon code to apply
	 * @param array            $metaData                    optional - additional data (if supported by IXOPLAN
	 *                                                      installation)
	 * @param bool             $useFlexible                 use the existing flexible payment method from the user to
	 *                                                      pay for the package change immediately
	 * @param User|int|string  $userTokenOrId               User authentication token or user ID.
	 * @param string|null      $strategyIdentifier          Which strategy to use. Uses default strategy when not provided
     * @param array            $addonSubscriptionMetadata
	 *
	 * @return SubscriptionChangeResponse
	 */
	public function subscriptionChange(
		$subscription,
		$newPackageIdentifier,
		$addonPackageIdentifiers = [],
		$couponCode = '',
		$metaData = [],
		$useFlexible = false,
		$userTokenOrId = null,
		$strategyIdentifier = null,
        $addonSubscriptionMetadata = []
	) {
		$data = [
			'subscriptionId'       =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'newPackageIdentifier' => $newPackageIdentifier,
		];
		if ($addonPackageIdentifiers) {
			$data['addonPackageIdentifiers'] = $addonPackageIdentifiers;
		}
		if ($couponCode) {
			$data['couponCode'] = $couponCode;
		}
		if ($metaData) {
			$data['metaData'] = $metaData;
		}
		if ($strategyIdentifier) {
			$data['strategyIdentifier'] = $strategyIdentifier;
		}
        if ($addonSubscriptionMetadata) {
            $data['addonSubscriptionMetadata'] = $addonSubscriptionMetadata;
        }
		$data['useFlexible'] = $useFlexible;
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CHANGE, $data);
		return SubscriptionChangeResponse::fromResponse($response);
	}

	/**
	 * Check if a coupon code is valid.
	 *
	 * @param string      $couponCode
	 * @param string|null $event @see self::COUPON_EVENT_*
	 *
	 * @return CouponCodeCheckResponse
	 */
	public function couponCodeCheck(
		$couponCode,
		$event = null
	) {
		$data = [
			'couponCode' => $couponCode,
		];
		if ($event) {
			$data['event'] = $event;
		}
		$response = $this->request(self::API_URI_COUPON_CODE_CHECK, $data);
		return CouponCodeCheckResponse::fromResponse($response, $couponCode, $event);
	}

	/**
	 * Closes a subscription immediately
	 *
	 * @param Subscription|int $subscription      the id of the subscription you want to close
	 * @param string           $closeReason       optional - the reason why the subscription was closed (should be
	 *                                            predefined reasons by your frontend)
	 * @param User|int|string  $userTokenOrId     User authentication token or user ID.
	 *
	 * @return SubscriptionCloseResponse
	 */
	public function subscriptionClose(
		$subscription,
		$closeReason = '',
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'closeReason'    => $closeReason,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CLOSE, $data);
		return SubscriptionCloseResponse::fromResponse($response);
	}

	/**
	 * Continues a previously cancelled subscription (undo cancellation).
	 *
	 * @param Subscription|int $subscription  the id of the subscription you want to close
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return SubscriptionContinueResponse
	 */
	public function subscriptionContinue(
		$subscription,
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CONTINUE, $data);
		return SubscriptionContinueResponse::fromResponse($response);
	}

	/**
	 * Create an addon subscription.
	 *
	 * @param Subscription|int $subscription
	 * @param string[]         $packageIdentifiers
	 * @param string           $couponCode
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return SubscriptionCreateAddonResponse
	 */
	public function subscriptionCreateAddon(
		$subscription,
		$packageIdentifiers,
		$couponCode = '',
		$userTokenOrId
	) {
		$data = [
			'subscriptionId'     =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'packageIdentifiers' => $packageIdentifiers,
		];
		if ($couponCode) {
			$data['couponCode'] = $couponCode;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CREATE_ADDON, $data);
		return SubscriptionCreateAddonResponse::fromResponse($response);
	}

    /**
     * Create a new subscription for a user, with optional addons.
     *
     * NOTE: users are locked to one currency code once their first subscription is created. You MUST pass the
     * users currency code in $currencyCode if it is already set up. You can obtain the currency code via
     * userGetBalance.
     *
     * NOTE: Always observe the needsBilling flag in the response. If it is true, call createPayment afterwards. If
     * it is false, you can use createFlexible to register a payment method without a payment. Don't mix up the two!
     *
     * @param User|int|string $userTokenOrId User authentication token or user ID.
     * @param string          $packageIdentifier
     * @param string          $currencyCode
     * @param string          $couponCode
     * @param array           $addonPackageIdentifiers
     * @param array           $metadata
     * @param array           $addonMetadata
     *
     * @return SubscriptionCreateResponse
     */
	public function subscriptionCreate(
		$userTokenOrId,
		$packageIdentifier,
		$currencyCode,
		$couponCode = '',
		$addonPackageIdentifiers = [],
        $metadata = [],
        $addonMetadata = []
	) {
		$data = [
			'packageIdentifier' => $packageIdentifier,
			'currencyCode'      => $currencyCode,
		];
		if ($addonPackageIdentifiers) {
			$data['addonPackageIdentifiers'] = $addonPackageIdentifiers;
		}
		if ($couponCode) {
			$data['couponCode'] = $couponCode;
		}
        if ($metadata) {
            $data['metadata'] = $metadata;
        }
        if ($addonMetadata) {
            $data['addonMetadata'] = $addonMetadata;
        }
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CREATE, $data);
		return SubscriptionCreateResponse::fromResponse($response);
	}

    /**
     * Create a new addonSubscription.
     *
     * @param User|int|string $userTokenOrId User authentication token or user ID.
     * @param $subscriptionId
     * @param $packageIdentifiers
     * @param string $couponCode
     * @param array $addonMetadata
     * @return SubscriptionCreateResponse
     * @throws DisloException
     * @throws ObjectNotFoundException
     */
    public function addonSubscriptionCreate(
        $userTokenOrId,
        $subscriptionId,
        $packageIdentifiers,
        $couponCode = '',
        $addonMetadata = []
    ) {
        $data = [
            'subscriptionId' => $subscriptionId,
            'packageIdentifiers'      => $packageIdentifiers,
        ];
        if ($couponCode) {
            $data['couponCode'] = $couponCode;
        }
        if ($addonMetadata) {
            $data['addonSubscriptionMetadata'] = $addonMetadata;
        }
        $this->userToData($userTokenOrId, $data);
        $response = $this->request('/frontend/subscription/createAddonSubscription', $data);
        return SubscriptionCreateResponse::fromResponse($response);
    }

	/**
	 * Change the package for an external subscription.
	 *
	 * @param Subscription|int $subscription                the unique subscription id to change
	 * @param string           $newPackageIdentifier        the identifier for the new plan.
	 * @param \DateTime        $newPeriodEnd                end date, has to be >= now.
	 * @param string[]         $addonPackageIdentifiers     optional - package identifiers of the addons
	 * @param null             $newExternalId               if provided, a new external profile will be created for the
	 *                                                      given subscription, the old one is invalidated
	 * @param array            $extraData                   required when newExternalId is set, key value data for
	 *                                                      external profile
	 * @param User|int|string  $userTokenOrId               User authentication token or user ID.
	 *
	 * @return SubscriptionExternalChangeResponse
	 */
	public function subscriptionExternalChange(
		$subscription,
		$newPackageIdentifier,
		\DateTime $newPeriodEnd,
		$addonPackageIdentifiers = [],
		$newExternalId = null,
		$extraData = [],
		$userTokenOrId = null
	) {
		$newPeriodEnd = clone $newPeriodEnd;
		$newPeriodEnd->setTimezone(new \DateTimeZone('UTC'));
		$data = [
			'subscriptionId'       =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'newPackageIdentifier' => $newPackageIdentifier,
			'newPeriodEnd'         => $newPeriodEnd->format('Y-m-d H:i:s'),
		];
		if ($addonPackageIdentifiers) {
			$data['addonPackageIdentifiers'] = $addonPackageIdentifiers;
		}
		if ($newExternalId) {
			$data['newExternalId'] = $newExternalId;
			$data['extraData']     = $extraData;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_EXTERNAL_CHANGE, $data);
		return SubscriptionExternalChangeResponse::fromResponse($response);
	}

	/**
	 * Change the period end of an external subscription
	 *
	 * @param Subscription|int $subscription
	 * @param \DateTime        $newPeriodEnd
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return SubscriptionExternalChangePeriodResponse
	 */
	public function subscriptionExternalChangePeriod(
		$subscription,
		\DateTime $newPeriodEnd,
		$userTokenOrId = null
	) {
		$newPeriodEnd = clone $newPeriodEnd;
		$newPeriodEnd->setTimezone(new \DateTimeZone('UTC'));
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'newPeriodEnd'   => $newPeriodEnd->format('Y-m-d H:i:s'),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_EXTERNAL_CHANGE_PERIOD, $data);
		return SubscriptionExternalChangePeriodResponse::fromResponse($response);
	}

	/**
	 * Closes an external subscription immediately.
	 *
	 * @param Subscription|int $subscription
	 * @param string           $closeReason
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return SubscriptionExternalCloseResponse
	 */
	public function subscriptionExternalClose(
		$subscription,
		$closeReason = '',
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		if ($closeReason) {
			$data['closeReason'] = $closeReason;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_EXTERNAL_CLOSE, $data);
		return SubscriptionExternalCloseResponse::fromResponse($response);
	}

	/**
	 * Create an external addon subscription.
	 *
	 * @param Subscription|int $subscription
	 * @param string[]         $packageIdentifiers
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return SubscriptionExternalAddonCreateResponse
	 */
	public function subscriptionExternalAddonCreate(
		$subscription,
		$packageIdentifiers,
		$userTokenOrId
	) {
		$data = [
			'subscriptionId'     =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'packageIdentifiers' => $packageIdentifiers,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_EXTERNAL_CREATE_ADDON_SUBSCRIPTION, $data);
		return SubscriptionExternalAddonCreateResponse::fromResponse($response);
	}

	/**
	 * Create an external subscription.
	 *
	 * @param string          $packageIdentifier            the package for the subscription
	 * @param string          $externalId                   unique id for the external profile that is created for this
	 *                                                      subscription
	 * @param array           $extraData                    key/value array where you can save whatever you need with
	 *                                                      the external profile, you can fetch this later on by
	 *                                                      passing the externalId to "billing/externalGetProfile"
	 * @param string          $currencyCode                 currency which should be used for the user
	 * @param array           $addonPackageIdentifiers      optional - additional addon packages
	 * @param \DateTime|null  $periodEnd                    end of the first period, if omitted, IXOPLAN will calculate
	 *                                                      the period end itself by using the package duration
	 * @param User|int|string $userTokenOrId                User authentication token or user ID.
	 *
	 * @return SubscriptionExternalCreateResponse
	 */
	public function subscriptionExternalCreate(
		$packageIdentifier,
		$externalId,
		$extraData,
		$currencyCode,
		$addonPackageIdentifiers = [],
		\DateTime $periodEnd = null,
		$userTokenOrId
	) {
		$data = [
			'packageIdentifier'       => $packageIdentifier,
			'externalId'              => $externalId,
			'extraData'               => $extraData,
			'currencyCode'            => $currencyCode,
			'addonPackageIdentifiers' => $addonPackageIdentifiers,
		];
		if ($periodEnd) {
			$periodEnd = clone $periodEnd;
			$periodEnd->setTimezone(new \DateTimeZone('UTC'));
			$data['periodEnd'] = $periodEnd->format('Y-m-d H:i:s');
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_EXTERNAL_CREATE, $data);
		return SubscriptionExternalCreateResponse::fromResponse($response);
	}

	/**
	 * Call a service provider function related to the subscription. Specific calls depend on the SPI connected to
	 * the service behind the subscription.
	 *
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 * @param Subscription|int $subscriptionId
	 * @param string           $method
	 * @param array            $params
	 * @param int|null         $serviceId
	 *
	 * @return SubscriptionCallSpiResponse
	 */
	public function subscriptionCallSpi(
		$userTokenOrId,
		$subscriptionId,
		$method,
		$params,
		$serviceId = null
	) {
		if ($subscriptionId instanceof Subscription) {
			$subscriptionId = $subscriptionId->getSubscriptionId();
		}
		$data = [
			'subscriptionId' => $subscriptionId,
			'method' => $method,
			'params' => $params,
			'serviceId' => $serviceId
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_CALL_SPI, $data);
		return SubscriptionCallSpiResponse::fromResponse($response);
	}

    /**
     * @param User|int|string  $userTokenOrId
     * @param Subscription|int $subscriptionId
     * @param string           $type
     * @param string|null      $strategyIdentifier
     *
     * @return SubscriptionGetPossibleUpgradesResponse
     */
	public function subscriptionGetPossibleUpgrades(
		$userTokenOrId,
		$subscriptionId,
		$type = '',
		$strategyIdentifier = null
	) {
		$data = [
			'subscriptionId' =>
				($subscriptionId instanceof Subscription?$subscriptionId->getSubscriptionId():$subscriptionId)
		];
		if ($type) {
			$data['type'] = $type;
		}
		if ($strategyIdentifier) {
			$data['strategyIdentifier'] = $strategyIdentifier;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_GET_POSSIBLE_PLAN_CHANGES, $data);
		return SubscriptionGetPossibleUpgradesResponse::fromResponse($response);
	}

	/**
	 * Retrieve all possible plan change strategies
	 * @return SubscriptionGetPossiblePlanChangeStrategiesResponse
	 * @throws DisloException
	 * @throws ObjectNotFoundException
	 */
	public function subscriptionGetPossiblePlanChangeStrategies() {
		$response = $this->request(self::API_URI_SUBSCRIPTION_GET_POSSIBLE_PLAN_CHANGE_STRATEGIES, []);
		return SubscriptionGetPossiblePlanChangeStrategiesResponse::fromResponse($response);
	}

	/**
	 * @param string $packageIdentifier
	 *
	 * @return PackageGetResponse
	 * @throws ObjectNotFoundException
	 */
	public function packageGet(
		$packageIdentifier
	) {
		$packages = $this->packagesList(null)->getPackages();
		foreach ($packages as $package) {
			if ($package->getPackageIdentifier() == $packageIdentifier) {
				return new PackageGetResponse($package);
			}
		}
		throw new ObjectNotFoundException('package with ID ' . $packageIdentifier);
	}

    /**
     * Retrieve a list of all packages registered in the system.
     *
     * @param string|null $serviceIdentifier
     *
     * @param array       $packageIdentifiers
     * @param bool        $onlyAvailable
     *
     * @return PackagesListResponse
     */
	public function packagesList(
		$serviceIdentifier = null,
        array $packageIdentifiers = [],
        $onlyAvailable = false
	) {
		$data = [];
		if ($serviceIdentifier) {
			$data['serviceIdentifier'] = $serviceIdentifier;
		}
        if(count($packageIdentifiers)) {
            $data['packageIdentifiers'] = $packageIdentifiers;
        }
        if ($onlyAvailable) {
            $data['onlyAvailable'] = true;
        }

		$response = $this->request(self::API_URI_PACKAGE_LIST, $data);

		return PackagesListResponse::fromResponse($response);
	}

	/**
	 * Retrieves a single subscription by its id.
	 *
	 * @param Subscription|int $subscription
	 * @param User|int|string  $userTokenOrId User authentication token or user ID.
	 *
	 * @return SubscriptionGetResponse
	 */
	public function subscriptionGet(
		$subscription,
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId' =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_GET, $data);
		return SubscriptionGetResponse::fromResponse($response);
	}

    /**
     * Retrieves all subscriptions for a user.
     *
     * @param User|int|string $userTokenOrId User authentication token or user ID.
     * @param array           $statusWhitelist
     * @param int|null        $limit
     *
     * @return SubscriptionGetAllResponse
     */
	public function subscriptionGetAll(
		$userTokenOrId,
        array $statusWhitelist = [],
        $limit = null
	) {
	    $data = [];

	    if (!empty($statusWhitelist)) {
	        $data['statusWhitelist'] = $statusWhitelist;
        }
        if (!empty($limit)) {
	        $data['limit'] = $limit;
        }

		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_GET_ALL, $data);
		return SubscriptionGetAllResponse::fromResponse($response);
	}

    /**
     * @param int                  $subscriptionId
     * @param User|int|string|null $userTokenOrId
     * @param int                  $limit
     * @param int                  $offset
     * @param string               $orderDir
     *
     * @return SubscriptionGetPeriodEventsResponse
     */
	public function subscriptionGetPeriodEvents(
        $subscriptionId,
        $userTokenOrId = null,
        $limit = 10,
        $offset = 0,
        $orderDir = self::ORDER_DIR_ASC
    ) {
        $data = [
            'subscriptionId' => $subscriptionId,
            'limit'          => $limit,
            'offset'         => $offset,
            'orderDir'       => $orderDir,
        ];

        $data = $this->userToData($userTokenOrId, $data);

        $response = $this->request(self::API_URI_SUBSCRIPTION_GET_PERIOD_EVENTS, $data);
        return SubscriptionGetPeriodEventsResponse::fromResponse($response);
    }

	/**
	 * Attach a coupon to a Subscription.
	 *
	 * @param string $couponCode
	 * @param Subscription|int $subscription
	 * @param User|int|string $userTokenOrId
	 *
	 * @return SubscriptionAttachCouponResponse
	 */
	public function subscriptionAttachCoupon(
		$couponCode,
		$subscription,
		$userTokenOrId = null
	) {
		$data = [
			'couponCode'              => $couponCode,
			'subscriptionId'          =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_ATTACH_COUPON, $data);
		return SubscriptionAttachCouponResponse::fromResponse($response);
	}

	/**
	 * Fires a subscription custom frontend API event to be handled by IXOPLAN's event engine.
	 *
	 * @param Subscription|int $subscription
	 * @param string $eventType To be evaluated in "Compare Custom Event Type" conditions
	 * @param array|null $notificationData Custom data for notification actions
	 * @param array|null $threadValueStoreData Key/value pairs for thread value store conditions and actions
	 * @param User|int|string $userTokenOrId If given, verify that subscription belongs to this user
	 *
	 * @return SubscriptionFireEventResponse
	 */
	public function subscriptionFireEvent(
		$subscription,
		$eventType,
		$notificationData = null,
		$threadValueStoreData = null,
		$userTokenOrId = null
	) {
		$data = [
			'subscriptionId' => ($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
			'eventType' => $eventType,
		];
		if ($notificationData) {
			$data['notificationData'] = $notificationData;
		}
		if ($threadValueStoreData) {
			$data['threadValueStoreData'] = $threadValueStoreData;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_SUBSCRIPTION_FIRE_EVENT, $data);
		return SubscriptionFireEventResponse::fromResponse($response);
	}

    /**
     * @param array $metaData
     * @param       $planIdentifier
     * @param null  $subscriptionId
     * @param null  $userTokenOrId
     *
     * @return SubscriptionValidateMetaDataResponse
     *
     * @throws InvalidRequestParameterException
     */
	public function subscriptionValidateMetaData(
        array $metaData,
        $planIdentifier,
        $subscriptionId = null,
        $userTokenOrId = null
    ) {
        $data = [
            'metaData'       => $metaData,
            'planIdentifier' => $planIdentifier,
        ];
        if (!empty($subscriptionId)) {
            $data['subscriptionId'] = $subscriptionId;
        }
        $data = $this->userToData($userTokenOrId, $data);
        $response = $this->request('/frontend/subscription/validateMetaData', $data);
        return SubscriptionValidateMetaDataResponse::fromResponse($response);
    }

	/**
	 * Check if a coupon is valid for the given context package/addons/event/user/sub and calculates the discounted
	 * price, for new subscriptions.
	 *
	 * @param string $couponCode
	 * @param string $packageIdentifier
	 * @param array  $addonPackageIdentifiers
	 * @param string $currencyCode
	 *
	 * @return CouponCodeValidateResponse
	 */
	public function couponCodeValidateNew(
		$couponCode,
		$packageIdentifier,
		$addonPackageIdentifiers = [],
		$currencyCode
	) {
		$data     = [
			'couponCode'              => $couponCode,
			'packageIdentifier'       => $packageIdentifier,
			'addonPackageIdentifiers' => $addonPackageIdentifiers,
			'event'                   => self::COUPON_EVENT_START,
			'currencyCode'            => $currencyCode,
		];
		$response = $this->request(self::API_URI_COUPON_CODE_VALIDATE, $data);
		return CouponCodeValidateResponse::fromResponse($response, $couponCode, self::COUPON_EVENT_START);
	}

	/**
	 * Check if a coupon is valid for the given context package/addons/event/user/sub and calculates the discounted
	 * price, for upgrades
	 *
	 * @param string           $couponCode
	 * @param string           $packageIdentifier
	 * @param array            $addonPackageIdentifiers
	 * @param string           $currencyCode
	 * @param User|string|int  $userTokenOrId
	 * @param Subscription|int $subscription
	 *
	 * @return CouponCodeValidateResponse
	 */
	public function couponCodeValidateUpgrade(
		$couponCode,
		$packageIdentifier,
		$addonPackageIdentifiers,
		$currencyCode,
		$subscription,
		$userTokenOrId = null
	) {
		$data = [
			'couponCode'              => $couponCode,
			'packageIdentifier'       => $packageIdentifier,
			'addonPackageIdentifiers' => $addonPackageIdentifiers,
			'event'                   => self::COUPON_EVENT_UPGRADE,
			'currencyCode'            => $currencyCode,
			'subscriptionId'          =>
				($subscription instanceof Subscription ? $subscription->getSubscriptionId() : $subscription),
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_COUPON_CODE_VALIDATE, $data);
		return CouponCodeValidateResponse::fromResponse($response, $couponCode, self::COUPON_EVENT_UPGRADE);
	}

	/**
	 * Authenticate a user. Returns an access token for subsequent API calls.
	 *
     * @param string      $username      Username.
     * @param string      $password      User password.
     * @param string      $ipAddress     IP address of the user attempting to authenticate.
     * @param int         $tokenLifetime Authentication token lifetime in seconds. TokenLifeTime is renewed and extended
     *                                   by API calls automatically, using the inital tokenlifetime.
     * @param string      $metainfo      Meta information to store with token (4096 bytes)
     * @param bool        $ignoreRateLimit
     * @param string|null $language
     *
	 * @return UserAuthenticateResponse
	 * @throws AuthenticationException
	 * @throws AuthenticationInvalidCredentialsException
	 * @throws AuthenticationRateLimitedException
	 * @throws DisloException
	 * @throws ObjectNotFoundException
	 * @throws \Exception
	 */
	public function userAuthenticate(
		$username,
		$password,
		$ipAddress,
		$tokenLifetime = 1800,
		$metainfo = '',
		$ignoreRateLimit = false,
        $language = null
	) {
        $data = [
            'username'        => $username,
            'password'        => $password,
            'ipAddress'       => $ipAddress,
            'tokenlifetime'   => \round($tokenLifetime / 60),
            'metainfo'        => $metainfo,
            'ignoreRateLimit' => $ignoreRateLimit,
            'language'        => $language,
        ];
		$response = $this->request(self::API_URI_USER_AUTHENTICATE, $data);

		if (isset($response['error'])) {
			switch ($response['error']) {
				case 'rate_limit':
					throw new AuthenticationRateLimitedException($username);
				case 'invalid_credentials':
					throw new AuthenticationInvalidCredentialsException($username);
				default:
					throw new AuthenticationException($username);
			}
		}

		return UserAuthenticateResponse::fromResponse($response);
	}

	/**
	 * Deauthenticate a token.
	 *
	 * @param string $authToken
	 *
	 * @return UserDeauthenticateResponse
	 */
	public function userDeauthenticate(
		$authToken
	) {
		$data     = [
			'authToken' => $authToken,
		];
		$response = $this->request(self::API_URI_USER_DEAUTHENTICATE, $data);
		return UserDeauthenticateResponse::fromResponse($response);
	}

	/**
	 * Change data of an existing user.
	 *
	 * @param User|string|int $userTokenOrId the unique user id to change
	 * @param string          $language      iso-2-letter language key to use for this user
	 * @param string[]        $metaData      meta data for this user (such as first name, last names etc.). NOTE: these
	 *                                       meta data keys must exist in the meta data profile in Distribload
	 *
	 * @return UserChangeResponse
	 */
	public function userChange(
		$userTokenOrId,
		$language,
		$metaData
	) {
		$data = [
			'language' => $language,
			'metaData' => $metaData,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_CHANGE, $data);
		return UserChangeResponse::fromResponse($response);
	}

    /**
     * Change metadata of an existing subscription.
     *
     * @param string $userTokenOrId the unique user id to change
     * @param string $subscriptionId the unique subscription id to change
     * @param string[]        $metadata      meta data for this user (such as first name, last names etc.). NOTE: these
     *                                       meta data keys must exist in the meta data profile in Distribload
     *
     * @return SubscriptionMetadataChangeResponse
     */
    public function subscriptionMetadataChange(
        $userTokenOrId,
        $subscriptionId,
        $metadata
    ) {
        $data = [
            'subscriptionId' => $subscriptionId,
        ];
        if($metadata){
            $data['metadata'] = $metadata;
        }
        $this->userToData($userTokenOrId, $data);
        $response = $this->request('/frontend/subscription/changeMetadata', $data);
        return SubscriptionMetadataChangeResponse::fromResponse($response);
    }

    /**
     * @param string $serviceIdentifier
     *
     * @return SubscriptionGetMetadataElementsResponse
     */
    public function subscriptionGetMetadataElements($serviceIdentifier)
    {
        $response = $this->request('/frontend/subscription/getMetadataElements', ['serviceIdentifier' => $serviceIdentifier]);
        return SubscriptionGetMetadataElementsResponse::fromResponse($response);
    }

    /**
     * @param array $data
     * @return CaptchaVerifyResponse
     * @throws DisloException on error
     */
	public function captchaVerify(array $data) {
		$response = $this->request('/frontend/misc/captcha/verify', $data);
		return CaptchaVerifyResponse::fromResponse($response);
	}

	/**
     * @param array $data
     * @return CaptchaCreateResponse
     * @throws DisloException on error
     */
	public function captchaCreate(array $data) {
		$response = $this->request('/frontend/misc/captcha/create', $data);
		return CaptchaCreateResponse::fromResponse($response);
	}

	/**
	 * Change password of an existing user.
	 *
	 * @param User|string|int $userTokenOrId the unique user id to change
	 * @param string          $newPassword   the new password
	 *
	 * @return UserChangeResponse
	 */
	public function userChangePassword(
		$userTokenOrId,
		$newPassword
	) {
		$data = [
			'plaintextPassword' => $newPassword,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_CHANGE_PASSWORD, $data);
		return UserChangeResponse::fromResponse($response);
	}

	/**
	 * Creates a new user with the given meta data.
	 *
     * @param string      $language          iso-2-letter language key to use for this user
     * @param string      $plaintextPassword password for this user
     * @param string[]    $metaData          meta data for this user (such as first name, last names etc.). NOTE: these
     *                                       meta data keys must exist in the meta data profile in Distribload
     * @param string|null $metaprofileName
	 *
	 * @return UserCreateResponse
	 */
	public function userCreate(
		$language,
		$plaintextPassword,
		$metaData,
        $metaprofileName = null
	) {
		$data     = [
			'language'          => $language,
			'plaintextPassword' => $plaintextPassword,
			'metaData'          => $metaData,
            'metaprofileName'   => $metaprofileName
		];
		$response = $this->request(self::API_URI_USER_CREATE, $data);
		return UserCreateResponse::fromResponse($response);
	}

	/**
	 * Soft-delete a user.
	 *
	 * @param User|string|int $userTokenOrId
	 *
	 * @return UserDeleteResponse
	 */
	public function userDelete(
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_DELETE, $data);
		return UserDeleteResponse::fromResponse($response);
	}

	/**
	 * Disable website login capability for user.
	 *
	 * @param User|string|int $userTokenOrId
	 *
	 * @return UserDisableLoginResponse
	 */
	public function userDisableLogin(
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_DISABLE_LOGIN, $data);
		return UserDisableLoginResponse::fromResponse($response);
	}

	/**
	 * Enable website login capability for user.
	 *
	 * @param User|string|int $userTokenOrId
	 *
	 * @return UserEnableLoginResponse
	 */
	public function userEnableLogin(
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_ENABLE_LOGIN, $data);
		return UserEnableLoginResponse::fromResponse($response);
	}

	/**
	 * Get a user's balance.
	 *
	 * @param User|string|int $userTokenOrId
	 *
	 * @return UserGetBalanceResponse
	 */
	public function userGetBalance(
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_GET_ACCOUNT_BALANCE, $data);
		return UserGetBalanceResponse::fromResponse($response);
	}

    /**
     * Retrieve a list of metadata elements.
     *
     * @param string|null $metapPofileName
     *
     * @return UserGetMetaProfileResponse
     */
	public function userGetMetaProfile($metapPofileName = null)
    {
		$data     = ['metaprofileName' => $metapPofileName];
		$response = $this->request(self::API_URI_USER_GET_META_PROFILE, $data);
		return UserGetMetaProfileResponse::fromResponse($response);
	}

	/**
	 * Retrieves the users authentication tokens.
	 *
	 * @param User|string|int $userTokenOrId
	 *
	 * @return UserGetTokensResponse
	 */
	public function userGetTokens(
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_GET_AUTH_TOKENS, $data);
		return UserGetTokensResponse::fromResponse($response);
	}

	/**
	 * Retrieves a user.
	 *
	 * @param User|string|int $userTokenOrId
	 *
	 * @return UserGetResponse
	 */
	public function userGet(
		$userTokenOrId
	) {
		$data = [];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_GET, $data);
		return UserGetResponse::fromResponse($response);
	}

	/**
	 * Update a users AuthToken MetaInfo
	 *
	 * @param string $authToken
	 * @param string $metaInfo
	 * @param string $ipAddress
	 *
	 * @return UserUpdateTokenResponse
	 */
	public function userUpdateToken(
		$authToken,
		$metaInfo,
		$ipAddress = ''
	) {
		$data = [
			'authToken' => $authToken,
			'metaInfo'  => $metaInfo,
		];
		if ($ipAddress) {
			$data['ipAddress'] = $ipAddress;
		}
		$response = $this->request(self::API_URI_USER_UPDATE_AUTH_TOKEN, $data);
		return UserUpdateTokenResponse::fromResponse($response);
	}

	/**
	 * Extend a users AuthToken expiry time
	 *
	 * @param string   $authToken
	 * @param string   $ipAddress
	 * @param int|null $tokenLifetime   Omit to use lifetime set initially
	 *
	 * @return UserUpdateTokenResponse
	 */
	public function userExtendToken(
		$authToken,
		$ipAddress = '',
		$tokenLifetime = null
	) {
		$data = [
			'authToken' => $authToken,
		];
		if ($ipAddress) {
			$data['ipAddress'] = $ipAddress;
		}
		if (isset($tokenLifetime)) {
			$data['tokenlifetime'] = \round($tokenLifetime / 60);
		}
		$response = $this->request(self::API_URI_USER_EXTEND_AUTH_TOKEN, $data);
		return UserUpdateTokenResponse::fromResponse($response);
	}

    /**
     * Get user with validated frontend auth token.
     *
     * @param string $authToken
     * @param string $ipAddress
     *
     * @return UserGetAuthenticatedResponse
     */
	public function userGetAuthenticated(
	    $authToken,
        $ipAddress = ''
    ) {
        $data = [
            'authToken' => $authToken,
        ];

        if ($ipAddress) {
            $data['ipAddress'] = $ipAddress;
        }

        $response = $this->request(self::API_URI_USER_GET_AUTHENTICATED, $data);
        return UserGetAuthenticatedResponse::fromResponse($response);
    }

	/**
	 * Searches among the unique properties of all users in order to find one user. The search term must match exactly.
	 *
     * @param string   $searchTerm
     * @param int|null $metaprofileName
	 *
	 * @return UserFindResponse
	 *
	 * @throws ObjectNotFoundException
	 */
	public function userFind($searchTerm, $metaprofileName = null) {
		$response = $this->request(
            self::API_URI_USER_FIND,
            [
                'searchTerm'      => $searchTerm,
                'metaprofileName' => $metaprofileName,
            ]
        );
		return UserFindResponse::fromResponse($response);
	}


	/**
	 * Start the user recovery process.
	 *
	 * @param string $userIdentifier Unique identifier for the user needing recovery.
	 * @param string $ipAddress      IP address of the request.
	 * @param string $resetLink      Link the user can click to do password recovery. %s will be replaced with the
	 *                               recovery code.
	 *
	 * @return UserRecoveryStartResponse
	 */
	public function userRecoveryStart($userIdentifier, $ipAddress, $resetLink) {
		$response = $this->request(self::API_URI_USER_RECOVERY_START, [
			'identifier' => $userIdentifier,
			'ipAddress' => $ipAddress,
			'resetLink' => $resetLink
		]);
		return UserRecoveryStartResponse::fromResponse($response);
	}

	/**
	 * Check if a given token is valid.
	 *
	 * @param string $recoveryToken
	 * @param string $ipAddress
	 *
	 * @return UserRecoveryCheckResponse
	 */
	public function userRecoveryCheck($recoveryToken, $ipAddress) {
		$response = $this->request(self::API_URI_USER_RECOVERY_CHECK, [
			'recoveryToken' => $recoveryToken,
			'ipAddress' => (string)$ipAddress
		]);
		return UserRecoveryCheckResponse::fromResponse($response);
	}

	/**
	 * Finish the account recovery process.
	 *
	 * @param string $recoveryToken
	 * @param string $ipAddress
	 * @param string $newPassword
	 *
	 * @return UserRecoveryFinishResponse
	 *
	 * @throws ObjectNotFoundException
	 */
	public function userRecoveryFinish($recoveryToken, $ipAddress, $newPassword) {
		$response = $this->request(self::API_URI_USER_RECOVERY_FINISH, [
			'recoveryToken' => $recoveryToken,
			'ipAddress' => $ipAddress,
			'plaintextPassword' => $newPassword
		]);
		return UserRecoveryFinishResponse::fromResponse($response);
	}

	/**
	 * Starts the User Verification ProcessWhen using this call, the user will receive an email to his stored email
	 * address which gives instruction on how to verify. The email message must be configured by using the template
	 * "token-verification".
	 *
	 * @param string|int|User $userTokenOrId
	 * @param string          $ipAddress
	 * @param string          $verificationLink
	 *
	 * @return UserEmailVerificationStartResponse
	 */
	public function userEmailVerificationStart(
		$userTokenOrId,
		$ipAddress,
		$verificationLink
	) {
		$data = [
			'verificationLink' => $verificationLink,
			'ipAddress' => (string)$ipAddress,
			'verificationType' => 'email',
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_VERIFICATION_START, $data);
		return UserEmailVerificationStartResponse::fromResponse($response);
	}

	/**
	 * Finalizes the users verification Process
	 *
	 * @param $verificationToken
	 *
	 * @return UserEmailVerificationFinishResponse
	 */
	public function userEmailVerificationFinish(
		$verificationToken
	) {
		$data = [
			'verificationToken' => $verificationToken,
			'verificationType' => 'email'
		];
		$response = $this->request(self::API_URI_USER_VERIFICATION_FINISH, $data);
		return UserEmailVerificationFinishResponse::fromResponse($response);

	}

	/**
	 * @param string|int|User $userTokenOrId
	 * @param string          $ipAddress
	 * @param string          $phoneNumber
	 *
	 * @return UserPhoneVerificationStartResponse
	 */
	public function userPhoneVerificationStart(
		$userTokenOrId,
		$ipAddress,
		$phoneNumber
	) {
		$data = [
			'verificationType' => 'phone',
			'ipAddress' => (string)$ipAddress,
			'extraData' => [
				'phoneNumber' => $phoneNumber
			]
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_VERIFICATION_START, $data);
		return UserPhoneVerificationStartResponse::fromResponse($response);
	}

	/**
	 * @param string|int|User $userTokenOrId
	 * @param string          $verificationPin
	 * @param string|null	  $phoneNumber
	 *
	 * @return UserPhoneVerificationFinishResponse
	 */
	public function userPhoneVerificationFinish(
		$userTokenOrId,
		$verificationPin,
		$phoneNumber = null
	) {
		$data = [
			'verificationType' => 'phone',
			'verificationPin' => $verificationPin,
			'phoneNumber' => $phoneNumber,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_VERIFICATION_FINISH, $data);
		return UserPhoneVerificationFinishResponse::fromResponse($response);
	}

	/**
	 * @param string|int|User $userTokenOdId
	 * @param string          $ipAddress
	 * @param string          $phoneNumber
	 *
	 * @return UserVerificationStartResponse
	 */
	public function userSmsVerificationStart(
		$userTokenOdId,
		$ipAddress,
		$phoneNumber
	) {
		$data = [
			'verificationType' => 'sms',
			'ipAddress' => (string)$ipAddress,
			'extraData' => [
				'phoneNumber' => $phoneNumber
			]
		];
		$this->userToData($userTokenOdId, $data);
		$response = $this->request(self::API_URI_USER_VERIFICATION_START, $data);
		return UserSmsVerificationStartResponse::fromResponse($response);
	}

	/**
	 * @param string|int|User $userTokenOrId
	 * @param string          $verificationPin
	 * @param string|null	  $phoneNumber
	 *
	 * @return UserSmsVerificationFinishResponse
	 */
	public function userSmsVerificationFinish(
		$userTokenOrId,
		$verificationPin,
		$phoneNumber = null
	) {
		$data = [
			'verificationType' => 'sms',
			'verificationPin' => $verificationPin,
			'phoneNumber' => $phoneNumber,
		];
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_VERIFICATION_FINISH, $data);
		return UserSmsVerificationFinishResponse::fromResponse($response);
	}

	/**
	 * Fires an user custom frontend API event to be handled by IXOPLAN's event engine.
	 *
	 * @param string|int|User $userTokenOrId
	 * @param string $eventType To be evaluated in "Compare Custom Event Type" conditions
	 * @param array|null $notificationData Custom data for notification actions
	 * @param array|null $threadValueStoreData Key/value pairs for thread value store conditions and actions
	 *
	 * @return UserFireEventResponse
	 */
	public function userFireEvent(
		$userTokenOrId,
		$eventType,
		$notificationData = null,
		$threadValueStoreData = null
	) {
		$data = [
			'eventType' => $eventType,
		];
		if ($notificationData) {
			$data['notificationData'] = $notificationData;
		}
		if ($threadValueStoreData) {
			$data['threadValueStoreData'] = $threadValueStoreData;
		}
		$this->userToData($userTokenOrId, $data);
		$response = $this->request(self::API_URI_USER_FIRE_EVENT, $data);
		return UserFireEventResponse::fromResponse($response);
	}

    /**
     * @param array                $metaData
     * @param string|null          $metaProfileName
     * @param string|int|User|null $userTokenOrId
     *
     * @return UserValidateMetaDataResponse
     */
	public function userValidateMetaData(array $metaData, $metaProfileName = null, $userTokenOrId = null)
    {
        $data = ['metaData' => $metaData];
        if (!empty($metaProfileName)) {
            $data['metaprofileName'] = $metaProfileName;
        }
        $data = $this->userToData($userTokenOrId, $data);

        $response = $this->request('/frontend/user/validateMetaData', $data);
        return UserValidateMetaDataResponse::fromResponse($response);
    }

	/**
	 * Flags an email as opened
	 *
	 * @param int $emailId The identifier from the email beacon
	 * @param string $checksum The checksum from the email beacon
	 *
	 * @return MailTrackOpenedResponse
	 */
	public function mailTrackOpened(
		$emailId,
		$checksum
	) {
		$data = [
			'emailId' => $emailId,
			'checksum' => $checksum,
		];
		$response = $this->request(self::API_URI_TRACK_OPENED_MAIL, $data);
		return MailTrackOpenedResponse::fromResponse($response);
	}

	/**
	 * Retrieve IXOPLAN's redirector configuration
	 *
	 * @return MiscGetRedirectorConfigurationResponse
	 */
	public function miscGetRedirectorConfiguration() {
		$data = [];
		$response = $this->request(self::API_URI_REDIRECTOR_GET_CONFIGURATION, $data);
		return MiscGetRedirectorConfigurationResponse::fromResponse($response);
	}

	/**
	 * Run a stored report against IXOPLAN's search database streaming the returned data. Requires a RequestClient with
	 * streaming support.
	 *
	 * @param int             $reportId as shown in IXOPLAN's administrator interface
	 * @param array|null      $parameters name/value pairs to fill placeholders within the report
	 * @param mixed|null      $stream String, resource, object or interface to stream the response body to, default to stdout
	 *
	 * @return StreamInterface
	 */
	public function exportStreamReport(
		$reportId,
		$parameters = null,
		$stream = null
	) {
		$data = [];
		if ($parameters) {
			$data['parameters'] = $parameters;
		}
		if (!$stream) {
			$stream = \fopen('php://stdout', 'w');
		}
		return $this->getRequestClientExtra()->requestStream(self::API_URI_EXPORT_STREAM_REPORT . $reportId, $data, $stream);
	}

	/**
	 * Run a query against IXOPLAN's search database streaming the returned data. Requires a RequestClient with
	 * streaming support.
	 *
	 * @param string          $query SQL statement to execute, may contain ":_name(type)" placeholders
	 * @param array|null      $parameters name/value pairs to fill placeholders within the query
	 * @param mixed|null      $stream String, resource, object or interface to stream the response body to, default to stdout
	 *
	 * @return StreamInterface
	 */
	public function exportStreamQuery(
		$query,
		$parameters = null,
		$stream = null
	) {
		$data = [
			'query' => $query
		];
		if ($parameters) {
			$data['parameters'] = $parameters;
		}
		if (!$stream) {
			$stream = \fopen('php://stdout', 'w');
		}
		return $this->getRequestClientExtra()->requestStream(sel, $data, $stream);
	}

	/**
	 * Run a stored report against IXOPLAN's search database returning the result as string. Keep memory limits in mind!
	 *
	 * @param int             $reportId as shown in IXOPLAN's administrator interface
	 * @param array|null      $parameters name/value pairs to fill placeholders within the report
	 *
	 * @return string
	 */
	public function exportReport(
		$reportId,
		$parameters = null
	) {
		return $this->exportStreamReport($reportId, $parameters, \fopen('php://temp', 'w+'))->getContents();
	}

	/**
	 * Run a query against IXOPLAN's search database returning the result as string. Keep memory limits im mind!
	 *
	 * @param string          $query SQL statement to execute, may contain ":_name(type)" placeholders
	 * @param array|null      $parameters name/value pairs to fill placeholders within the query
	 *
	 * @return string
	 */
	public function exportQuery(
		$query,
		$parameters = null
	) {
		return $this->exportStreamQuery($query, $parameters, \fopen('php://temp', 'w+'))->getContents();
	}

    /**
     * Return System Details
     * @return array
     * @throws DisloException
     */
	public function systemInfoGet() {
		return $this->request(self::API_URI_SYSTEM_INFO, []);
	}


}
