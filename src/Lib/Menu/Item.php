<?php

namespace IDT\LaravelCommon\Lib\Menu;
interface Item
{
	public function route(string $name, array $parameters = []): Item;

	public function icon(?string $icon = null): Item;

	public function isVisible(): bool;

	public function toArray(): array;

	public function build(Menu $menu): Item;

	public function isGroup(): bool;

	public function getRoute(): ?string;

	public function isActive(): bool;

	/** @return Item[] */
	public function getChildren(): array;

	public function enabled(bool $enabled = true): Item;

	public function visible(bool $visible = true): Item;
}
