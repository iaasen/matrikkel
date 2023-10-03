<?php
/**
 * User: ingvar.aasen
 * Date: 14.09.2023
 */

namespace Iaasen\Matrikkel\Client;

/**
 * @method findObjekterForAdresse(array $request)
 */
class AdresseClient extends AbstractSoapClient {
	const WSDL = [
		'prod' => 'https://matrikkel.no/matrikkelapi/wsapi/v1/AdresseServiceWS?WSDL',
		'test' => 'https://prodtest.matrikkel.no/matrikkelapi/wsapi/v1/AdresseServiceWS?WSDL',
	];
}