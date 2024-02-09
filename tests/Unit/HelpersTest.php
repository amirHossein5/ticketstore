<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\Wormhole;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @group slow
     *
     * @test
     */
    public function generates_almost_unique_code()
    {
        $tries = 100000;
        $codes = [];

        for ($i = 0; $i <= $tries; $i++) {
            $code = short_code();

            if (in_array($code, $codes)) {
                $this->fail("{$code} is already generated with {$tries} tries.");
            }

            $codes[] = $code;
        }

        $this->expectNotToPerformAssertions();
    }

    /** @test */
    public function ticket_code_has_prefix()
    {
        $travel = fn (...$params) => new Wormhole(...$params);
        $travel(rand(-50, 50))->month();

        $code = short_code();

        $prefix = now()->format('y').now()->month.now()->day;
        $this->assertStringStartsWith($prefix, $code);
        $this->assertEquals(strlen($prefix.'_') + 7, strlen($code));
    }
}
