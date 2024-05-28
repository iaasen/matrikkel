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
			'matrikkel:adresse-import' => \Iaasen\Matrikkel\Console\AddressImportCommand::class,
			'matrikkel:bruksenhet' => \Iaasen\Matrikkel\Console\BruksenhetCommand::class,
			'matrikkel:kodeliste' => \Iaasen\Matrikkel\Console\KodelisteCommand::class,
			'matrikkel:kommune' => \Iaasen\Matrikkel\Console\KommuneCommand::class,
			'matrikkel:matrikkelenhet' => \Iaasen\Matrikkel\Console\MatrikkelenhetCommand::class,
			'matrikkel:ping' => \Iaasen\Matrikkel\Console\PingCommand::class,
			'matrikkel:sok' => \Iaasen\Matrikkel\Console\MatrikkelsokCommand::class,
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
