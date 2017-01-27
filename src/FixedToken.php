<?php

namespace GisoStallenberg\FixedToken;

use InvalidArgumentException;
use RuntimeException;

/**
 * Generate and verify tokens based on a secret and some public data.
 *
 * @author giso
 */
class FixedToken
{
    /**
     * A secret used to create tokens.
     */
    private $tokenSecret;

    /**
     * Some public info to use to generate and verify the token.
     *
     * @var array
     */
    private $data = array();

    /**
     * Create a new FixedToken class.
     *
     * @param string $tokenSecret
     */
    public function __construct($tokenSecret)
    {
        $this->tokenSecret = $tokenSecret;
    }

    /**
     * Create a new FixedToken class in a static way.
     *
     * @param string $tokenSecret
     */
    public static function create($tokenSecret)
    {
        return new static($tokenSecret);
    }

    /**
     * Adds public info to use to generate and verify the token.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return FixedToken
     */
    public function addData($key, $value = null)
    {
        if (is_array($key) && $value !== null) {
            throw new InvalidArgumentException('You cannot set data using an array and also provide a value');
        }
        if (is_array($key) && $value === null) {
            foreach ($key as $dataKey => $dataValue) {
                $this->addData($dataKey, $dataValue);
            }

            return $this;
        }
        $this->data[$key] = $value;

        ksort($this->data);

        return $this;
    }

    /**
     * Generates a token.
     *
     * @throws RuntimeException
     */
    public function generate()
    {
        if (empty($this->data)) {
            throw new RuntimeException('Please set some data before generating a token');
        }

        $publicBase = http_build_query($this->data);

        return sha1($this->tokenSecret.$publicBase);
    }

    /**
     * Verifies the token.
     *
     * @param string $token
     *
     * @return bool
     */
    public function verify($token)
    {
        return ($this->generate() === $token);
    }
}
