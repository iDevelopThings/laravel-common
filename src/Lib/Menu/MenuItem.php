<?php

namespace IDT\LaravelCommon\Lib\Menu;

use IDT\LaravelCommon\Lib\Utils\Attributes;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @extends Attributes<MenuItem>
 */
class MenuItem extends Attributes implements Arrayable, Item
{

    public ?string $title = null;

    public ?RouteInfo $route = null;

    public bool $enabled = false;

    public bool $active   = false;
    public bool $expanded = false;

    public ?string $icon = null;

    public ?MenuItem $parent = null;
    /**
     * @var Item[] $children
     */
    public array $children = [];


    public function __construct(string $title, bool $enabled = true, Item $parent = null)
    {
        $this->title   = $title;
        $this->enabled = $enabled;
        $this->parent  = $parent;
    }

    public function route(string $name, array $parameters = []): Item
    {
        $this->route = new RouteInfo([
            'name'       => $name,
            'parameters' => $parameters,
        ]);

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->enabled;
    }

    public function icon(?string $icon = null): Item
    {
        $this->icon = $icon;

        return $this;
    }

    public function toArray(): array
    {
        $data = array_merge($this->getAttributes(), [
            'title'    => $this->title,
            'route'    => $this->route?->toArray(),
            'enabled'  => $this->enabled,
            'active'   => $this->active,
            'expanded' => $this->expanded,
            'children' => array_map(fn(Item $item) => $item->toArray(), $this->children),
        ]);

        if ($this->icon) {
            $data['icon'] = $this->icon;
        }

        return $data;
    }

    public function build(Menu $menu): Item
    {
        if ($this->route && $menu->currentRoute) {
            $this->active = $this->compareRoutePath($menu->currentRoute->getName(), $this->route->name);

            if ($this->active && $this->parent instanceof MenuGroup) {
                $this->parent->active   = true;
                $this->parent->expanded = true;
            }

            if (!empty($this->children)) {
                foreach ($this->children as $child) {
                    $child->build($menu);
                }
            }
        }

        return $this;
    }

    public function isGroup(): bool
    {
        return $this instanceof MenuGroup || !empty($this->children);
    }

    public function getRoute(): ?string
    {
        if (!$this->route) {
            return null;
        }

        return route($this->route->name, $this->route->parameters);
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /** @return Item[] */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function enabled(bool $enabled = true): Item
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function visible(bool $visible = true): Item
    {
        $this->enabled = $visible;

        return $this;
    }

    public function getDepth(): int
    {
        if (!$this->route?->name) {
            return 0;
        }

        return count(explode('.', $this->route->name));
    }

    private function compareRoutePath($activeRouteName, ?string $name)
    {
        if (!$name) {
            return false;
        }

        if ($activeRouteName === $name) {
            return true;
        }

        $activeRouteName = explode('.', $activeRouteName);

        $activeRouteName = array_slice($activeRouteName, 0, count(explode('.', $name)));

        $activeRouteName = implode('.', $activeRouteName);

        return $activeRouteName === $name;
    }
}
