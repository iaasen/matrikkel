<?php
/**
 * User: ingvar.aasen
 * Date: 29.04.2024
 */

namespace Iaasen\Matrikkel\Client;

class BruksenhetClient extends AbstractSoapClient {
	const WSDL = [
		'prod' => 'https://matrikkel.no/matrikkelapi/wsapi/v1/BruksenhetServiceWS?WSDL',
		'test' => 'https://prodtest.matrikkel.no/matrikkelapi/wsapi/v1/BruksenhetServiceWS?WSDL',
	];
}
