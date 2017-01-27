<?php

namespace GisoStallenberg\FixedToken\Tests;

use GisoStallenberg\FixedToken\FixedToken;
use PHPUnit\Framework\TestCase;

/**
 * Test the fixed token library
 *
 * @author Giso Stallenberg
 */
class FixedTokenTest extends TestCase
{
    /**
     * Secret used for tests
     *
     * @var string
     */
    private $tokenSecret = '.POhVpv9uMLFoOMhCpLW1cz7pE';

    /**
     * @dataProvider tokenAndDataProvider
     */
    public function testTokenGeneration($token, $data)
    {
        $this->assertTrue(FixedToken::create($this->tokenSecret)
            ->addData($data)
            ->verify($token)
        );
    }

    /**
     * @dataProvider tokenAndDataProvider
     */
    public function testAddingDataOneByOne($token, $data)
    {
        $fixedToken = FixedToken::create($this->tokenSecret);

        foreach ($data as $key => $value) {
            $fixedToken->addData($key, $value);
        }

        $this->assertTrue($fixedToken->verify($token));
    }

    /**
     * @dataProvider tokenAndDataProvider
     */
    public function testAddingDataArrayAndValueThrowsException($token, $data)
    {
        $this->expectException('InvalidArgumentException');

        $this->assertTrue(FixedToken::create($this->tokenSecret)
            ->addData($data, 'a-value')
            ->verify($token)
        );
    }

    /**
     * Test to see if generating before adding public data throws a RuntimeException
     */
    public function testPrematurGeneratingThrowsException()
    {
        $this->expectException('RuntimeException');

        FixedToken::create($this->tokenSecret)
            ->generate();
    }

    /**
     * Test to see if verifying before adding public data throws a RuntimeException
     */
    public function testPrematurVerifyingThrowsException()
    {
        $this->expectException('RuntimeException');

        FixedToken::create($this->tokenSecret)
            ->verify('5d2d331acb615eac02ea6747caaaae11');
    }

    /**
     * Test to see if verifying wrong token fails
     *
     * @dataProvider tokenAndDataProvider
     */
    public function testRandomTokenFails($token, $data)
    {
        $this->assertFalse(FixedToken::create($this->tokenSecret)
            ->addData($data)
            ->verify('5d2d331acb615eac02ea6747caaaae11')
        );
    }

    /**
     * Provide some data
     *
     * @return array
     */
    public function tokenAndDataProvider()
    {
        return array(
            array('c8cb9fa42ce54077797c234ed8c3e654', array(
                'username' => 'giso',
            )),
            array('0dc827ce743b33e375a6965391c3ef81', array(
                'id' => 1,
                'foo' => 'bar',
            )),
        );
    }
}