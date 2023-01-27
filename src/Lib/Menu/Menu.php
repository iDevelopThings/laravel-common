<?php

namespace IDT\LaravelCommon\Lib\Menu;
interface Menu
{
	public function add(string $title, ?string $routeName = null, array $routeParameters = []): Item;

	public function group(string $title, callable $callback): MenuGroup;

	public function configure(): void;

	public function getActiveItems(): array;

	public function getActiveItem(): Item|MenuGroup|null;

	/**
	 * @return Item[]
	 */
	public function getItems(): array;

	public function build(): Menu;

	public function toArray(): array;
}
