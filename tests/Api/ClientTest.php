<?php

namespace Skiftet\Speakout\Tests\Api;

use InvalidArgumentException;
use Skiftet\Speakout\Api\Client as Speakout;
use PHPUnit\Framework\TestCase;
use Skiftet\Speakout\Models\Action;
use Skiftet\Speakout\Models\Campaign;

class ClientTest extends TestCase
{

    private $speakout;

    private static $env = [
        'SPEAKOUT_API_ENDPOINT' => null,
        'SPEAKOUT_API_USER' => null,
        'SPEAKOUT_API_PASSWORD' => null,
        'SPEAKOUT_API_TEST_EMAIL' => null,
    ];

    public static function setUpBeforeClass(): void
    {
        foreach (self::$env as $key => &$value) {
            $value = getenv($key);
            if (!$value) {
                throw new InvalidArgumentException(
                    "$key needs to be set"
                );
            }
        }
    }

    protected function setUp(): void
    {
        $this->speakout = new Speakout([
            'endpoint' => self::$env['SPEAKOUT_API_ENDPOINT'],
            'user'     => self::$env['SPEAKOUT_API_USER'],
            'password' => self::$env['SPEAKOUT_API_PASSWORD'],
        ]);
    }

    public function tearDown(): void
    {
        $this->speakout = null;
    }

    /**
     *
     */
    public function testCampaignListing()
    {
        $this->assertIsArray($this->speakout->campaigns()->all());
    }

    /**
     *
     */
    public function testCampaignGet()
    {
        $campaign = $this->speakout->campaigns()->find(1);
        $this->assertArrayHasKey('id', $campaign);
        return $campaign;
    }

    /**
     * @depends testCampaignGet
     * @param Campaign $campaign
     */
    public function testActionInCampaigns(Campaign $campaign)
    {
        $this->assertIsArray($campaign->actions()->all());
    }

    /**
     * @depends testCampaignGet
     */
    public function testCreateActionInCampaign(Campaign $campaign)
    {
        $action = $campaign->actions()->create([
            'email' => self::$env['SPEAKOUT_API_TEST_EMAIL'],
            'firstname' => 'Test',
            'lastname' => 'Testsson',
            'postcode' => 12345,
            'action_type' => 'rsign',
        ]);

        $this->assertInstanceOf(Action::class, $action);
        $this->assertGreaterThanOrEqual(1, $action['id']);
    }

    /**
     *
     */
    public function testActionListing()
    {
        $this->assertIsArray($this->speakout->actions()->all());
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
        $this->assertIsString($campaigns[0]->setClient($this->speakout)->url());
    }
}
