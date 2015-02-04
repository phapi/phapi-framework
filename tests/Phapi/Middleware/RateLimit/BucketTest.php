<?php

namespace Phapi\Tests\Middleware\RateLimit;

use Phapi\Middleware\RateLimit\Bucket;


/**
 * @coversDefaultClass \Phapi\Middleware\RateLimit\Bucket
 */
class BucketTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $bucket = new Bucket(999, 400, 10, false);
        $this->assertEquals(999, $bucket->totalTokens);
        $this->assertEquals(400, $bucket->newTokens);
        $this->assertEquals(10, $bucket->newTokensWindow);
        $this->assertFalse($bucket->newTokenContinuous);
        $this->assertEquals(0, $bucket->remainingTokens);
    }

}
