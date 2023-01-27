<?php

namespace IDT\LaravelCommon\Lib\Menu;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

abstract class BaseMenu implements Menu, Arrayable
{

	/**
	 * @var Item[]
	 */
	public array $items = [];

	public ?Route $currentRoute = null;
	/**
	 * @var (MenuItem|MenuGroup)[]
	 */
	private array $builtItems = [];

	/**
	 * @var (MenuItem|MenuGroup)[]
	 */
	private array $active = [];

	public function __construct(?Request $request = null)
	{
		$request = $request ?? app(Request::class);

		if ($request) {
			$this->currentRoute = $request->route();
		}
	}

	public function add(string $title, ?string $routeName = null, array $routeParameters = []): Item
	{
		$item = new MenuItem($title);
		if ($routeName) {
			$item->route($routeName, $routeParameters);
		}

		$this->items[] = $item;

		return $item;
	}

	public function group(string $title, callable $callback): MenuGroup
	{
		$item = new MenuGroup($title);

		$this->items[] = $item;

		$callback($item);

		return $item;
	}

	public abstract function configure(): void;

	public function getActiveItems(): array
	{
		return $this->active;
	}

	public function getActiveItem(): Item|MenuGroup|null
	{
		if (empty($this->active)) {
			return null;
		}

		$item = $this->active[0];

		if ($item instanceof MenuGroup) {
			$deepestActive = collect($item->children)
				->filter(fn($item) => $item->active)
				->sortByDesc(fn($item) => $item->getDepth());

			return $deepestActive->first();
		}

		return $item;
	}

	public function getItems(): array
	{
		return $this->builtItems;
	}

	public function build(): Menu
	{
		$this->configure();

		$this->builtItems = [];

		foreach ($this->items as $item) {
			if ($item->isVisible()) {
				$item = $item->build($this);

				$this->builtItems[] = $item;
			}

			if ($item->active) {
				$this->active[] = $item;
			}
		}

		return $this;
	}

	public function toArray(): array
	{
		return collect($this->getItems())->map(fn($item) => $item->toArray())->toArray();
	}

}
