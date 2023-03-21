<?php

declare(strict_types=1);

namespace OCA\UnifiedPushProvider\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version010300 extends SimpleMigrationStep {

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

		if (!$schema->getTable('uppush_applications')) {
			$table = $schema->getTable('uppush_applications');
			$primaryKey = $table->getPrimaryKey();
			if (!$primaryKey) {
				$table->setPrimaryKey(['token']);
			}
		}
		if (!$schema->getTable('uppush_devices')) {
			$table = $schema->getTable('uppush_devices');
			$primaryKey = $table->getPrimaryKey();
			if (!$primaryKey) {
				$table->setPrimaryKey(['device_id']);
			}
		}
		if (!$schema->getTable('uppush_config')) {
			$table = $schema->getTable('uppush_config');
			$primaryKey = $table->getPrimaryKey();
			if (!$primaryKey) {
				$table->setPrimaryKey(['parameter']);
			}
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
