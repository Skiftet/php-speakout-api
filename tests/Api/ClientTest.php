<?php

namespace Skiftet\Speakout\Tests\Api;

use InvalidArgumentException;
use Skiftet\Speakout\Api\Client as Speakout;
use PHPUnit\Framework\TestCase;
use Skiftet\Speakout\Models\Campaign;

class ClientTest extends TestCase
{

    private $speakout;

    protected function setUp()
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

        $this->speakout = new Speakout([
            'endpoint' => $env['SPEAKOUT_API_ENDPOINT'],
            'user'     => $env['SPEAKOUT_API_USER'],
            'password' => $env['SPEAKOUT_API_PASSWORD'],
        ]);
    }

    public function tearDown()
    {
        $this->speakout = null;
    }

    public function testCampaignListing()
    {
        $this->assertInternalType('array', $this->speakout->campaigns()->all());
    }

    public function testCampaignGet()
    {
        $campaign = $this->speakout->campaigns()->find(1);
        $this->assertArrayHasKey('id', $campaign);
    }

    public function testActionListing()
    {
        $this->assertInternalType('array', $this->speakout->actions()->all());
    }

    public function testHydration()
    {
        $campaigns = $this->speakout->campaigns()->all();
        $this->assertNotCount(0, $campaigns);
        $this->assertInstanceOf(Campaign::class, $campaigns[0]);
        return $campaigns;
    }

    public function testGetHydration()
    {
        $campaigns = $this->speakout->campaigns()->query()->get();
        $this->assertInstanceOf(Campaign::class, $campaigns[0]);
    }

    /**
     * @depends testHydration
     */
    public function testSerialization($campaigns)
    {
        $serialized = serialize($campaigns);
        $this->assertInstanceOf(Campaign::class, unserialize($serialized)[0]);
        return $serialized;
    }

    /**
     * @depends testSerialization
     */
    public function testUrlAfterSerialize(string $serialized)
    {
        $campaigns = unserialize($serialized);
        $this->assertInternalType('string', $campaigns[0]->setClient($this->speakout)->url());
    }
}
