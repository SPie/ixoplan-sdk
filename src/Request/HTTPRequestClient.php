<?php

namespace Ixolit\Dislo\Request;

use Ixolit\Dislo\Exceptions\DisloException;
use Ixolit\Dislo\Exceptions\NotImplementedException;
use Ixolit\Dislo\HTTP\HTTPClientAdapter;
use Ixolit\Dislo\HTTP\HTTPClientAdapterExtra;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * This client uses a PSR-7 client to talk to Ixoplan.
 *
 * @package Ixolit\Dislo\Request
 */
class HTTPRequestClient implements RequestClient, RequestClientExtra, RequestClientWithDevModeSupport {

	/**
	 * @var HTTPClientAdapter
	 */
	private $httpClient;
	/**
	 * @var
	 */
	private $host;
	/**
	 * @var TimeProvider
	 */
	private $timeProvider;
	/**
	 * @var
	 */
	private $apiKey;
	/**
	 * @var
	 */
	private $apiSecret;

    /**
     * Disable https e.g. for development purposes
     * @var bool
     */
	private $https = true;

    /**
     * Enable/Disable the developer mode, which when enables might return different plans/billing methods etc. depending on the backend configuration
     * @var bool
     */
    private $devMode = false;

	/**
	 * @return HTTPClientAdapterExtra
	 *
	 * @throws NotImplementedException
	 */
	private function getHttpClientExtra() {
		if ($this->httpClient instanceof HTTPClientAdapterExtra) {
			return $this->httpClient;
		}
		else {
			throw new NotImplementedException();
		}
	}

	/**
	 * @param string $path
	 * @param array $params
	 *
	 * @return RequestInterface
	 */
	private function prepareRequest($path, array $params) {
		$payload   = \json_encode($params);
		$uri       = $this->httpClient->createUri()
			->withScheme($this->https ? 'https' : 'http')
			->withHost($this->host)
			->withPath($path)
			->withQuery(
				'api_key=' . \urlencode($this->apiKey) .
                '&timestamp=' . \urlencode($this->timeProvider->getTimestamp()).
                '&devMode=' . ($this->devMode ? 1 : 0)
			);
		$signature = \hash_hmac('MD5', $uri->getPath() . '?' . $uri->getQuery() . $payload, $this->apiSecret);
		$uri       = $uri->withQuery($uri->getQuery() . '&signature=' . \urlencode($signature));

		return $this->httpClient->createRequest()
			->withUri($uri)
			->withMethod('POST')
			->withHeader('Content-Type', 'text/json')
			->withBody($this->httpClient->createStringStream($payload));
	}

	/**
	 * @param HTTPClientAdapter $httpClient
	 * @param string            $host
	 * @param string            $apiKey
	 * @param string            $apiSecret
	 * @param TimeProvider|null $timeProvider
	 */
	public function __construct(
		HTTPClientAdapter $httpClient,
		$host,
		$apiKey,
		$apiSecret,
		TimeProvider $timeProvider = null
	) {
		if (!$timeProvider) {
			$timeProvider = new StandardTimeProvider();
		}
		$this->httpClient   = $httpClient;
		$this->host         = $host;
		$this->timeProvider = $timeProvider;
		$this->apiKey       = $apiKey;
		$this->apiSecret    = $apiSecret;
	}

	/**
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return array
	 *
	 * @throws DisloException
	 * @throws InvalidResponseData
	 */
	public function request($uri, array $params) {

		$request = $this->prepareRequest($uri, $params);

		$response = $this->httpClient->send($request);

		$decodedBody = \json_decode($response->getBody(), true);

		if (\json_last_error() == JSON_ERROR_NONE) {
			return $decodedBody;
		}

		throw new InvalidResponseData($response->getBody());
	}

	/**
	 * @param string $uri
	 * @param array $params
	 * @param mixed $stream
	 *
	 * @return StreamInterface
	 *
	 * @throws InvalidResponseData
	 */
	public function requestStream($uri, array $params, $stream) {

		$request = $this->prepareRequest($uri, $params);

		$response = $this->getHttpClientExtra()->sendAdvanced($request, [
			HTTPClientAdapterExtra::OPTION_RESPONSE_BODY_STREAM => $stream,
		]);

		if ($response->getStatusCode() == 200) {
			return $response->getBody();
		}

		throw new InvalidResponseData($response->getReasonPhrase(), $response->getStatusCode());
	}

    /**
     * @param bool $https
     * @return $this
     */
	public function useHttps($https) {
	    $this->https = (bool) $https;
	    return $this;
	}

    /**
     * Enable/disable the devMode
     * @param bool $enabled
     * @return $this
     */
    public function setDevMode($enabled) {
        $this->devMode = (bool) $enabled;
        return $this;
    }
}