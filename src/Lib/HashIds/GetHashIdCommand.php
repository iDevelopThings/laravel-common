<?php

namespace IDT\LaravelCommon\Lib\HashIds;

use Illuminate\Console\Command;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Str;
use Spatie\ModelInfo\ModelFinder;


class GetHashIdCommand extends Command
{
	protected $signature = 'hashid {model} {id}';

	protected $description = 'Get the hash id for a model id';

	public function handle()
	{
		$modelStr = $this->argument('model');
		$id       = $this->argument('id');

		$model = null;
		try {
			$model = resolve($modelStr);
		} catch (BindingResolutionException $exception) {
			$models = ModelFinder::all();
			$this->info('Searching all models for model...');
			$model = $models->first(fn($model) => Str::endsWith($model, $this->argument('model')));
		}

		if (!$model) {
			$this->error("Model: {$modelStr} not found");

			return;
		}

		$hash = HashIds::forModel($model);

		$this->components->twoColumnDetail(
			"Hash ID:",
			$hash->get($id)
		);
	}
}
