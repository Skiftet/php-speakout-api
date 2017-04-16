<?php

namespace Skiftet\Speakout\Tests\Api;

use InvalidArgumentException;
use Skiftet\Speakout\Api\Client as Speakout;
use PHPUnit\Framework\TestCase;
use Skiftet\Speakout\Models\Campaign;

class ClientTest extends TestCase
{

    /**
     *
     */
    public function testCampaignListing()
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

    public function testCampaignGet()
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
        $campaign = $speakout->campaigns()->find(1);
        $this->assertArrayHasKey('id', $campaign);
    }

    /**
     *
     */
    public function testActionListing()
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

        $this->assertInternalType('array', $speakout->actions()->all());
    }

    public function testHydration()
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

        $campaigns = $speakout->campaigns()->all();
        $this->assertNotCount(0, $campaigns);
        $this->assertInstanceOf(Campaign::class, $campaigns[0]);
        $this->assertInternalType('string', $campaigns[0]->url());
        return $campaigns;
    }

    /**
     * @depends testHydration
     */
    public function testSerialization($campaigns)
    {
        $this->assertInstanceOf(Campaign::class, unserialize(serialize($campaigns))[0]);
    }


}
