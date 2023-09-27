<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\MatrikkelApi\Entity;

/**
 * @property string $kommunenummer
 * @property string $kommunenavn
 * @property int $fylkeId
 */
class Kommune extends AbstractEntity {
	protected string $kommunenummer;
	protected string $kommunenavn;
	protected int $fylkeId;
	// There are more fields available from the API that are not included here


	public function setFylkeId(mixed $value) {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'fylkeId', $value);
	}

}
