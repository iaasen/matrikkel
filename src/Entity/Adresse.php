<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

/**
 * @property int $matrikkelenhetId
 * @property int[] $kretsIds
 * @property int $vegId
 * @property int $nummer
 * @property Veg $veg
 * @property Matrikkelenhet $matrikkelenhet
 */
class Adresse extends AbstractEntity {
	// TODO: $representasjonspunkt
	protected int $matrikkelenhetId;
	/** @var int[] */
	protected array $kretsIds;
	protected int $vegId;
	protected int $nummer;
	protected Veg $veg;
	protected Matrikkelenhet $matrikkelenhet;


	public function setMatrikkelenhetId($value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'matrikkelenhetId', $value);
	}


	public function setKretsIds(mixed $value) : void {
		if(is_object($value)) {
			$this->kretsIds = [];
			foreach($value->item AS $item) $this->kretsIds[] = $item->value;
		}
		else $this->setArrayInternal('int', 'kretsIds', $value);
	}


	public function setVegId(mixed $value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'vegId', $value);
	}

}
