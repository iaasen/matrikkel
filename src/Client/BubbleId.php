<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\MatrikkelApi\Client;

/**
 * Generates a SOAP_VAR used for "id" used by StoreService
 */
class BubbleId {
	public string $type;
	public string $value;

	const NAMESPACE_BASE = 'http://matrikkel.statkart.no/matrikkelapi/wsapi/v1/domain/';
	const NAMESPACES = [
		'KommuneId' => 'kommune',

	];


	public static function getId(string $objectType, string $id) : \SoapVar {
		$object = (new self())->setValue($id);
		$object->setType($objectType);
		return new \SoapVar($object, SOAP_ENC_OBJECT, $objectType, self::NAMESPACE_BASE . self::NAMESPACES[$objectType]);
	}


	public function setValue(string $value) : self {
		$this->value = $value;
		return $this;
	}


	public function setType(string $type) : self {
		$this->type = $type;
		return $this;
	}

}
