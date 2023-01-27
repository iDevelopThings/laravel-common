<?php

namespace IDT\LaravelCommon\Lib\Menu;

class ExampleMenu extends BaseMenu
{
    public function configure(): void
    {
        $this->add('Dashboard', "app.dashboard")->icon('Home-o');

        $this->group('Customers', function (MenuGroup $menu) {
            $menu->add('All Customers', 'app.customers.index');
            $menu->add('Add Customer', 'app.customers.create');
        })->route('app.customers.index')->icon('Users-o');

        $this->group('Webhooks', function (MenuGroup $menu) {
            $menu->add('All Webhooks', 'app.webhooks.index');
            $menu->add('View Webhook', 'app.webhooks.show')->visible(false);
            $menu->add('Create Webhook', 'app.webhooks.create');
        })->route('app.webhooks.index')->icon('Link-o');
    }
}
