<?php

namespace GisoStallenberg\FixedToken\tests;

use GisoStallenberg\FixedToken\FixedToken;
use PHPUnit\Framework\TestCase;

/**
 * Test the fixed token library.
 *
 * @author Giso Stallenberg
 */
class FixedTokenTest extends TestCase
{
    /**
     * Secret used for tests.
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
     * Test to see if generating before adding public data throws a RuntimeException.
     */
    public function testPrematurGeneratingThrowsException()
    {
        $this->expectException('RuntimeException');

        FixedToken::create($this->tokenSecret)
            ->generate();
    }

    /**
     * Test to see if verifying before adding public data throws a RuntimeException.
     */
    public function testPrematurVerifyingThrowsException()
    {
        $this->expectException('RuntimeException');

        FixedToken::create($this->tokenSecret)
            ->verify('5d2d331acb615eac02ea6747caaaae11');
    }

    /**
     * Test to see if verifying wrong token fails.
     *
     * @dataProvider tokenAndDataProvider
     */
    public function testRandomTokenFails($token, $data)
    {
        $this->assertFalse(FixedToken::create($this->tokenSecret)
            ->addData($data)
            ->verify('e30c8e120338e6a8e4c99c1e812f86fe58e55208')
        );
    }

    /**
     * Provide some data.
     *
     * @return array
     */
    public function tokenAndDataProvider()
    {
        return array(
            array('fdc2f8672dcb9df6b076ae68dd0a107c3d247200', array(
                'username' => 'giso',
            )),
            array('f01758b88eb3761b9c806d637259fe514fb0a9f2', array(
                'id' => 1,
                'foo' => 'bar',
            )),
        );
    }
}
