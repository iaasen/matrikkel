<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\Matrikkel\Entity\Krets\Grunnkrets;
use Iaasen\Matrikkel\Entity\Krets\Kirkesokn;
use Iaasen\Matrikkel\Entity\Krets\Krets;
use Iaasen\Matrikkel\Entity\Krets\Postnummeromrade;
use Iaasen\Matrikkel\Entity\Krets\Stemmekrets;
use Iaasen\Matrikkel\Entity\Krets\Svalbardomrade;
use Iaasen\Matrikkel\Entity\Krets\Tettsted;

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

	protected ?Postnummeromrade $postnummeromrade;
	protected ?Tettsted $tettsted;
	protected ?Kirkesokn $kirkesokn;
	protected ?Grunnkrets $grunnkrets;
	protected ?Stemmekrets $stemmekrets;
	protected ?Svalbardomrade $svalbardomrade;


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


	public function initializeKretser() : void {
		$this->postnummeromrade = null;
		$this->tettsted = null;
		$this->kirkesokn = null;
		$this->grunnkrets = null;
		$this->stemmekrets = null;
		$this->svalbardomrade = null;
	}


	public function addKrets(Krets $krets) : void {
		$propertyName = lcfirst(Krets::KRETSTYPER[$krets->kretstypeKodeId]);
		$this->$propertyName = $krets;
	}

}
