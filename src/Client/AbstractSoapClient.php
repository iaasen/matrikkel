<?php
/**
 * User: ingvar.aasen
 * Date: 14.09.2023
 */

namespace Iaasen\MatrikkelApi\Client;

use Iaasen\Exception\InvalidArgumentException;
use Iaasen\Exception\NotAuthenticatedException;
use Iaasen\Exception\NotFoundException;
use Laminas\Soap\Client;

class AbstractSoapClient extends Client {

	public function __construct($wsdl = null, $options = null) {
		parent::__construct($wsdl, $options);
		$this->setSoapVersion(SOAP_1_1);
	}


	public function __call($name, $arguments) : mixed {
		try {
			return parent::__call($name, $arguments);
		}
		catch (\SoapFault $e) {
			throw new \SoapFault($e->getCode(), $e->getMessage());
//			if($e->faultcode == 'S:Client') throw new InvalidArgumentException($e->getMessage());
//			if($e->faultcode == 'HTTP') throw new NotAuthenticatedException('Unable to login. Check that login or password is incorrect');
//			if($e->faultcode == 'S:Server') {
//				if(isset($e->detail?->ServiceException?->enc_stype) && $e->detail?->ServiceException?->enc_stype == 'ObjectsNotFoundFaultInfo') {
//					 throw new NotFoundException($e->detail->ServiceException->enc_value->exceptionDetail->message);
//				}
//			}
//			throw new \Exception($e->getMessage(), $e->getCode());
		}
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
