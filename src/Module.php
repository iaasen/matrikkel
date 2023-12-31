<?php
/**
 * User: ingvar.aasen
 * Date: 26.09.2023
 */

namespace Iaasen\Matrikkel;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface {
	public function getConfig() {
		return include __DIR__ . '/../config/module.config.php';
	}
}