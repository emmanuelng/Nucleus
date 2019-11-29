<?php

declare(strict_types=1);

namespace Nucleus\Router\Policies;

use Nucleus\Router\Exceptions\InvalidPolicyException;
use Nucleus\Router\Policy;

/**
 * This class loads a policy from a JSON file. A policy JSON file contains the
 * following fields:
 *
 * - **origins**: list of allowed origins. If all origins are allowed,
 *   the value must be `'*'`.
 * - **methods**: list of allowed methods. If all origins are allowed,
 *   the value must be `'*'`.
 * - **headers**: list of allowed response headers. If all origins are
 *   allowed, the value must be `'*'`.
 *
 * If any field isn't defined, the default value is used.
 *
 * **Example file**: policy.json
 * ```
 * {
 *      'origins': ['http://myWebsite.com'],
 *      'methods': ['GET', 'POST'],
 *      'headers': '*'
 * }
 * ```
 */
class PolicyFile implements Policy
{
    /**
     * The list of allowed origins.
     *
     * @var array
     */
    private $origins;

    /**
     * The list of allowed request methods.
     *
     * @var array
     */
    private $methods;

    /**
     * The list of allowed response headers.
     *
     * @var array
     */
    private $headers;

    /**
     * Initializes the policy.
     *
     * @param string $filePath The path of the JSON file that defines the
     * policy.
     */
    public function __construct(string $filePath)
    {
        // Read the file
        $policyArr = json_decode(file_get_contents($filePath), true);
        if ($policyArr === null) {
            throw new InvalidPolicyException("Invalid file content.");
        }

        // Set allowed origins.
        $defOrigins = Policy::DEFAULT_ORIGINS;
        $this->origins = $policyArr['origins'] ?? $defOrigins;
        if ($this->origins !== '*' && !is_array($this->origins)) {
            throw new InvalidPolicyException("Invalid allowed origins.");
        }

        // Set allowed methods.
        $defMethods = Policy::DEFAULT_METHODS;
        $this->methods = $policyArr['methods'] ?? $defMethods;
        if ($this->methods !== '*' && !is_array($this->methods)) {
            throw new InvalidPolicyException("Invalid allowed methods.");
        }

        // Set allowed response header.
        $defHeaders = Policy::DEFAULT_HEADERS;
        $this->headers = $policyArr['headers'] ?? $defHeaders;
        if ($this->headers !== '*' && !is_array($this->headers)) {
            throw new InvalidPolicyException("Invalid allowed headers.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function origins(): ?array
    {
        $allAccepted = count($this->origins) == 1 &&
            $this->origins[0] === '*';

        return $allAccepted ? null : $this->origins;
    }

    /**
     * {@inheritDoc}
     */
    public function methods(): ?array
    {
        $allAccepted = count($this->methods) == 1 &&
            $this->methods[0] === '*';

        return $allAccepted ? null : $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function headers(): ?array
    {
        $allAccepted = count($this->headers) == 1 &&
            $this->headers[0] === '*';

        return $allAccepted ? null : $this->headers;
    }
}
