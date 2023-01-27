<?php


use IDT\LaravelCommon\Tests\Fixtures\ExampleMenu;
use Illuminate\Http\Request;

it('menu gets built', function () {
    Route::get('/dashboard', fn() => 'dashboard')->name('dashboard');

    $request = Request::create('/dashboard');
    $request->setRouteResolver(fn() => app('router')->getRoutes()->match($request));

    $menu = new ExampleMenu($request);
    $menu->build();

    $items = $menu->toArray();

    $this->assertTrue($menu->getActiveItem()->title === 'Dashboard');
});

it('should have an active group', function () {

    Route::get('/app/customers', fn() => '/app/customers')->name('app.customers.index');

    $request = Request::create('/app/customers');
    $request->setRouteResolver(fn() => app('router')->getRoutes()->match($request));

    $menu = new ExampleMenu($request);
    $menu->build();

    $this->assertTrue($menu->getActiveItem()->title === 'Customers');
});
