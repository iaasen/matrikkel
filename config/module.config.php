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
			'matrikkel:adresse' => \Iaasen\Matrikkel\Console\AdresseCommand::class,
		],
	],
	'service_manager' => [
		'abstract_factories' => [
			\Iaasen\Matrikkel\Client\SoapClientFactory::class,
			ReflectionBasedAbstractFactory::class,
		],
		'factories' => [
//			\Iaasen\Matrikkel\Client\AdresseClient::class => \Iaasen\Matrikkel\Client\SoapClientFactory::class,
//			\Iaasen\Matrikkel\Client\KodelisteClient::class => \Iaasen\Matrikkel\Client\SoapClientFactory::class,
//			\Iaasen\Matrikkel\Client\KommuneClient::class => \Iaasen\Matrikkel\Client\SoapClientFactory::class,
//			\Iaasen\Matrikkel\Client\StoreClient::class => \Iaasen\Matrikkel\Client\SoapClientFactory::class,
		],
	],
];
