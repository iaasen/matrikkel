<?php
/**
 * User: ingvar.aasen
 * Date: 28.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\Model\AbstractEntityV2;

class Representasjonspunkt extends AbstractEntityV2 {
	// Lookup kodeliste id 3
	protected int $koordinatsystemKodeId;
	protected int $originalKoordinatsystemKodeId;
	// Lookup kodeliste id 1
	protected int $koordinatkvalitetKodeId;
	protected bool $stedfestingVerifisert;

	protected float $x;
	protected float $y;
	protected float $z;

	/**
	 * Lookup Kodeliste id 3 for full list
	 * 84 (lat/long) doesn't seem to be supported
	 */
	const KOORDINATSYSTEM_KODE_ID_OPTIONS = [
		21 => 'EUREF89 UTM Sone 31',
		22 => 'EUREF89 UTM Sone 32',
		23 => 'EUREF89 UTM Sone 33',
		84 => 'EUREF89 Geografisk', // Not working
	];


	public function setKoordinatsystemKodeId(mixed $value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'koordinatsystemKodeId', $value);
	}


	public function setOriginalKoordinatsystemKodeId(mixed $value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'originalKoordinatsystemKodeId', $value);
	}


	public function setKoordinatkvalitetKodeId(mixed $value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'koordinatkvalitetKodeId', $value);
	}


	public function setPosition(object $value) : void {
		$this->x = $value->x;
		$this->y = $value->y;
		$this->z = $value->z;
	}

}
