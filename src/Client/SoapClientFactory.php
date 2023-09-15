<?php
/**
 * User: ingvar.aasen
 * Date: 14.09.2023
 */

namespace Iaasen\MatrikkelApi\Client;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class SoapClientFactory implements FactoryInterface {

	public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : object {
		return new \stdClass();
		// TODO: Implement __invoke() method.
	}

	/** Symfony factory */
	public static function create(string $className) : AbstractSoapClient {
		return new $className(
			$className::WSDL[$_ENV['MATRIKKELAPI_ENVIRONMENT'] ?? 'prod'],
			['login' => $_ENV['MATRIKKELAPI_LOGIN'], 'password' => $_ENV['MATRIKKELAPI_PASSWORD']]
		);
	}
}