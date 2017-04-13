<?php

namespace Skiftet\Speakout\Tests\Api;

use InvalidArgumentException;
use Skiftet\Speakout\Api\Client as Speakout;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{

    /**
     *
     */
    public function testListing()
    {
        $env = [
            'SPEAKOUT_API_ENDPOINT' => null,
            'SPEAKOUT_API_USER' => null,
            'SPEAKOUT_API_PASSWORD' => null,
        ];

        foreach ($env as $key => &$value) {
            $value = getenv($key);
            if (!$value) {
                throw new InvalidArgumentException(
                    "$key needs to be set"
                );
            }
        }

        $speakout = new Speakout([
            'endpoint' => $env['SPEAKOUT_API_ENDPOINT'],
            'user'     => $env['SPEAKOUT_API_USER'],
            'password' => $env['SPEAKOUT_API_PASSWORD'],
        ]);
        $this->assertInternalType('array', $speakout->campaigns()->all());
    }
}
