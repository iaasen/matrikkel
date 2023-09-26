<?php

use Iaasen\Factory\ReflectionBasedAbstractFactory;

return [
	'matrikkel-api' => [
		'environment' => 'test',
		'login' => '',
		'password' => '',
	],
	'laminas-cli' => [
		'commands' => [
			'matrikkel:adresse' => \Iaasen\MatrikkelApi\Console\AdresseCommand::class,
		],
	],
	'service_manager' => [
		'abstract_factories' => [
			\Iaasen\MatrikkelApi\Client\SoapClientFactory::class,
			ReflectionBasedAbstractFactory::class,
		],
		'factories' => [
//			\Iaasen\MatrikkelApi\Client\AdresseClient::class => \Iaasen\MatrikkelApi\Client\SoapClientFactory::class,
//			\Iaasen\MatrikkelApi\Client\KodelisteClient::class => \Iaasen\MatrikkelApi\Client\SoapClientFactory::class,
//			\Iaasen\MatrikkelApi\Client\KommuneClient::class => \Iaasen\MatrikkelApi\Client\SoapClientFactory::class,
//			\Iaasen\MatrikkelApi\Client\StoreClient::class => \Iaasen\MatrikkelApi\Client\SoapClientFactory::class,
		],
	],
];
