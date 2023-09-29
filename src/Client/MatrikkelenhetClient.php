<?php
/**
 * User: ingvar.aasen
 * Date: 14.09.2023
 */

namespace Iaasen\Matrikkel\Client;

class MatrikkelenhetClient extends AbstractSoapClient {
	const WSDL = [
		'prod' => 'https://matrikkel.no/matrikkelapi/wsapi/v1/MatrikkelenhetServiceWS?WSDL',
		'test' => 'https://prodtest.matrikkel.no/matrikkelapi/wsapi/v1/MatrikkelenhetServiceWS?WSDL',
	];
}