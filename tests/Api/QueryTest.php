<?php

namespace Skiftet\Speakout\Tests\Api;

use PHPUnit\Framework\TestCase;
use Skiftet\Speakout\Api\BaseResource;
use Skiftet\Speakout\Api\Query;

class QueryTest extends TestCase
{
    public function testListing()
    {
        $mockClient = $this
            ->getMockBuilder(BaseResource::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $query = new Query($mockClient, '/campaigns');
        $mockClient
            ->expects($this->once())
            ->method('get')
            ->with('/campaigns')
            ->willReturn([])
        ;
        $query->get();
    }

    public function testOrderedListing()
    {
        $mockClient = $this
            ->getMockBuilder(BaseResource::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $query = new Query($mockClient, '/campaigns');
        $query->orderBy('actions');
        $mockClient
            ->expects($this->once())
            ->method('get')
            ->with('/campaigns', [
                'order_by' => 'actions'
            ])
            ->willReturn([])
        ;
        $query->get();
    }

    public function testOrderedSubQueryListing()
    {
        $mockClient = $this
            ->getMockBuilder(BaseResource::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $query = new Query($mockClient, '/campaigns');
        $query->has('actions', function(Query $subQuery) {
            return $subQuery->since('2017-03-01');
        });
        $mockClient
            ->expects($this->once())
            ->method('get')
            ->with('/campaigns', [
                'since.actions' => '2017-03-01',
            ])
            ->willReturn([])
        ;
        $query->get();
    }

}
