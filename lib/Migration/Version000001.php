<?php

declare(strict_types=1);

namespace OCA\UnifiedPushProvider\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version000001 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('uppush_applications')) {
			$table = $schema->createTable('uppush_applications');
			$table->addColumn('device_id', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('app_name', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('token', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('date', 'string', [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['token']);
		}
		if (!$schema->hasTable('uppush_devices')) {
			$table = $schema->createTable('uppush_devices');
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('device_id', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('device_name', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('date', 'string', [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['device_id']);
		}
		if (!$schema->hasTable('uppush_config')) {
			$table = $schema->createTable('uppush_config');
			$table->addColumn('parameter', 'string', [
				'notnull' => true,
			]);
			$table->addColumn('value', 'string', [
				'notnull' => true,
			]);
			$table->setPrimaryKey(['parameter']);
		}
		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}
}
