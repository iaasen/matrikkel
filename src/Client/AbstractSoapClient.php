<?php
/**
 * User: ingvar.aasen
 * Date: 14.09.2023
 */

namespace Iaasen\MatrikkelApi\Client;

use Laminas\Soap\Client;

class AbstractSoapClient extends Client {

	public function __construct($wsdl = null, $options = null) {
		parent::__construct($wsdl, $options);
		$this->setSoapVersion(SOAP_1_1);
	}


	public function _preProcessArguments($arguments) : mixed {
		$arguments[0]['matrikkelContext'] = $this->getMatrikkelContext();
		return $arguments;
	}


	public function getMatrikkelContext() : array {
		return [
			'locale' => 'no_NO',
			'brukOriginaleKoordinater' => false,
			'koordinatsystemKodeId' => ['value' => 22], // 22 = E89_32
			'systemVersion' => '4.4',
			'klientIdentifikasjon' => $this->getOptions()['login'],
			'setSnapshotVersion' =>	new \DateTime('9999-01-01 00:00:00'),
		];
	}

}
