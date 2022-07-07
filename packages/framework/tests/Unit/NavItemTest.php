<?php

namespace Hyde\Framework\Testing\Unit;

use Hyde\Framework\Contracts\PageContract;
use Hyde\Framework\Facades\Route;
use Hyde\Framework\Models\NavItem;
use Hyde\Framework\Modules\Routing\RouteContract;
use Hyde\Testing\TestCase;

/**
 * @covers \Hyde\Framework\Models\NavItem
 */
class NavItemTest extends TestCase
{
    public function test__construct()
    {
        $route = $this->createMock(RouteContract::class);
        $route->method('getSourceModel')->willReturn($this->createMock(PageContract::class));
        $route->method('getLink')->willReturn('/');

        $item = new NavItem($route, 'Test', 500, true);

        $this->assertSame($route, $item->route);
        $this->assertSame('Test', $item->title);
        $this->assertSame(500, $item->priority);
        $this->assertTrue($item->hidden);
    }

    public function testFromRoute()
    {
        $route = Route::get('index');
        $item = NavItem::fromRoute($route);

        $this->assertSame($route, $item->route);
        $this->assertSame('Home', $item->title);
        $this->assertSame(0, $item->priority);
        $this->assertFalse($item->hidden);
    }

    public function testResolveLink()
    {
        $route = Route::get('index');
        $item = NavItem::fromRoute($route);

        $this->assertSame('index.html', $item->resolveLink());
    }

    public function test__toString()
    {
        $route = Route::get('index');
        $item = NavItem::fromRoute($route);

        $this->assertSame('index.html', (string) $item);
    }
}