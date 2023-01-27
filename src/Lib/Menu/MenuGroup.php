<?php

namespace IDT\LaravelCommon\Lib\Menu;

class MenuGroup extends MenuItem implements Item
{
	public MenuGroup|MenuItem|null $parent = null;

	/**
	 * @var (MenuItem|MenuGroup)[] $children
	 */
	public array $children = [];

	public function __construct(string $title, bool $enabled = true, Item $parent = null)
	{
		parent::__construct($title, $enabled, $parent);
	}

	public function add(string $title, ?string $routeName = null, array $parameters = []): Item
	{
		$item = new MenuItem($title, true, $this);

		if ($routeName) {
			$item->route($routeName, $parameters);
		}

		$this->children[] = $item;

		return $item;
	}

	public function group(string $title, callable $callback): Item
	{
		$item = new MenuGroup($title, true, $this);

		$this->children[] = $item;

		$callback($item);

		return $item;
	}



}
