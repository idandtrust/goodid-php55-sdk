<?php
/**
 * Copyright 2017 ID&Trust, Ltd.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary form
 * for use in connection with the web services and APIs provided by ID&Trust.
 *
 * As with any software that integrates with the GoodID platform, your use
 * of this software is subject to the GoodID Terms of Service
 * (https://goodid.net/docs/tos).
 * This copyright notice shall be included in all copies or substantial portions
 * of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 */

namespace GoodID\Authentication\Endpoint;

use GoodID\Exception\GoodIDException;
use GoodID\Helpers\GoodIDServerConfig;
use GoodID\Helpers\Key\RSAPrivateKey;
use GoodID\Helpers\OpenIDRequestSource\OpenIDRequestSource;
use GoodID\Helpers\Request\IncomingRequest;
use GoodID\Helpers\SessionDataHandlerInterface;
use GoodID\Helpers\StateNonceHandler;

/**
 * Parent of the GoodID Endpoint classes
 */
abstract class AbstractGoodIDEndpoint
{
    /**
     * The smallest accepted value for max_age
     */
    const MAX_AGE_MIN_VALUE = 3600;

    /**
     * The largest accepted value for max_age
     * 60 days in seconds
     */
    const MAX_AGE_MAX_VALUE = 5184000;

    /**
     * @var IncomingRequest
     */
    protected $incomingRequest;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var RSAPrivateKey
     */
    protected $signingKey;

    /**
     * @var RSAPrivateKey
     */
    protected $encryptionKey;

    /**
     * @var OpenIDRequestSource
     */
    protected $requestSource;

    /**
     * @var string
     */
    protected $redirectUri;

    /**
     * @var int
     */
    protected $acr;

    /**
     *
     * @var int
     */
    protected $maxAge;

    /**
     * @var GoodIDServerConfig
     */
    protected $goodIdServerConfig;

    /**
     * @var SessionDataHandlerInterface
     */
    protected $sessionDataHandler;

    /**
     * @var StateNonceHandler
     */
    protected $stateNonceHandler;

    /**
     * For comments see GoodIDEndpointFactory
     *
     * @param IncomingRequest $incomingRequest
     * @param string $clientId
     * @param RSAPrivateKey $signingKey
     * @param RSAPrivateKey $encryptionKey
     * @param OpenIDRequestSource $requestSource
     * @param string $redirectUri
     * @param int $acr
     * @param GoodIDServerConfig $goodIdServerConfig
     * @param SessionDataHandlerInterface $sessionDataHandler
     * @param StateNonceHandler $stateNonceHandler
     * @param int|null $maxAge
     */
    public function __construct(
        IncomingRequest $incomingRequest,
        $clientId,
        RSAPrivateKey $signingKey,
        RSAPrivateKey $encryptionKey,
        OpenIDRequestSource $requestSource,
        $redirectUri,
        $acr,
        GoodIDServerConfig $goodIdServerConfig,
        SessionDataHandlerInterface $sessionDataHandler,
        StateNonceHandler $stateNonceHandler,
        $maxAge
    ) {
        if (empty($clientId)) {
            throw new GoodIDException('$clientId can not be empty');
        }

        if (!is_null($maxAge) &&
            (!is_int($maxAge) || $maxAge < self::MAX_AGE_MIN_VALUE || $maxAge > self::MAX_AGE_MAX_VALUE)
        ) {
            throw new GoodIDException('$maxAge must be null or an int in the range ['
                . self::MAX_AGE_MIN_VALUE .  ', ' . self::MAX_AGE_MAX_VALUE . ']');
        }

        $this->incomingRequest = $incomingRequest;
        $this->clientId = $clientId;
        $this->signingKey = $signingKey;
        $this->encryptionKey = $encryptionKey;
        $this->requestSource = $requestSource;
        $this->redirectUri = $redirectUri;
        $this->acr = $acr;
        $this->maxAge = $maxAge;
        $this->goodIdServerConfig = $goodIdServerConfig;
        $this->sessionDataHandler = $sessionDataHandler;
        $this->stateNonceHandler = $stateNonceHandler;
    }

    /**
     * This will run the main logic of the concrete endpoint
     * It might set headers, print messages, etc
     */
    abstract public function run();
}
