<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\MatrikkelApi\Entity;

/**
 * @property int $kommuneId
 * @property int $adressekode
 * @property string $adressenavn
 * @property string $kortAdressenavn
 * @property int $stedsnummer
 */
class Veg extends AbstractEntity {
	protected int $kommuneId;
	protected int $adressekode;
	protected string $adressenavn;
	protected string $kortAdressenavn;
	protected int $stedsnummer;


	public function setKommuneId($value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'kommuneId', $value);
	}

}
