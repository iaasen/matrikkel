<?php
/**
 * User: ingvar.aasen
 * Date: 28.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\Model\AbstractEntityV2;

/**
 * @property int $koordinatsystemKodeId
 * @property int $originalKoordinatsystemKodeId
 * @property int $koordinatkvalitetKodeId
 * @property bool $stedfestingVerifisert
 * @property float $x;
 * @property float $y;
 * @property float $z;
 */
class Representasjonspunkt extends AbstractEntityV2 {
	// Lookup kodeliste id 3
	protected int $koordinatsystemKodeId;
	protected int $originalKoordinatsystemKodeId;
	// Lookup kodeliste id 1
	protected int $koordinatkvalitetKodeId;
	protected bool $stedfestingVerifisert;

	protected float $x; //East/west
	protected float $y; // North/south
	protected float $z; // Height

	/**
	 * Lookup Kodeliste id 3 for full list
	 * 84 (lat/long) doesn't seem to be supported
	 */
	const KOORDINATSYSTEM_KODE_ID_OPTIONS = [
		9 => 'EUREF89 UTM Sone 31',
		10 => 'EUREF89 UTM Sone 32',
		11 => 'EUREF89 UTM Sone 33',
		24 => 'EUREF89 Geografisk', // Not working
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
