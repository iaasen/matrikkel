<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\MatrikkelApi\Client;

/**
 * @method findTekstelementerForAutoutfylling(array $request)
 */
class MatrikkelsokClient extends AbstractSoapClient {
	const WSDL = [
		'prod' => 'https://matrikkel.no/matrikkelapi/wsapi/v1/MatrikkelsokServiceWS?WSDL',
		'test' => 'https://prodtest.matrikkel.no/matrikkelapi/wsapi/v1/MatrikkelsokServiceWS?WSDL',
	];

}