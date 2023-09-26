<?php
/**
 * User: ingvar.aasen
 * Date: 14.09.2023
 */

namespace Iaasen\MatrikkelApi\Client;

use Iaasen\Exception\InvalidArgumentException;
use Iaasen\Service\LaminasMvcConfig;
use Laminas\ServiceManager\Factory\AbstractFactoryInterface;
use Psr\Container\ContainerInterface;

class SoapClientFactory implements AbstractFactoryInterface {

	/** Laminas abstract factory */
	public function canCreate(ContainerInterface $container, $requestedName) {
		return is_subclass_of($requestedName, AbstractSoapClient::class);
	}

	/** Laminas factory */
	public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null) : object {
		$config = $container->get(LaminasMvcConfig::class);
		if(!isset($config['matrikkel-api'])) throw new InvalidArgumentException('Config is missing key "matrikkel-api"');
		$config = $config['matrikkel-api'];

		return new $requestedName(
			$requestedName::WSDL[$config['environment'] ?? 'prod'],
			[
				'login' => $config['login'],
				'password' => $config['password'],
			],
		);
	}


	/** Symfony factory */
	public static function create(string $className) : AbstractSoapClient {
		return new $className(
			$className::WSDL[$_ENV['MATRIKKELAPI_ENVIRONMENT'] ?? 'prod'],
			['login' => $_ENV['MATRIKKELAPI_LOGIN'], 'password' => $_ENV['MATRIKKELAPI_PASSWORD']]
		);
	}
}