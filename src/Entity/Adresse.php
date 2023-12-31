<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\Matrikkel\Entity\Krets\Grunnkrets;
use Iaasen\Matrikkel\Entity\Krets\Kirkesokn;
use Iaasen\Matrikkel\Entity\Krets\Kommunalkrets;
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
 * @property ?string $bokstav
 * @property Veg $veg
 * @property Matrikkelenhet $matrikkelenhet
 * @property Representasjonspunkt $representasjonspunkt
 * @property Bruksenhet[] $bruksenheter
 * @property Postnummeromrade $postnummeromrade
 * @property Tettsted $tettsted
 * @property Kirkesokn $kirkesokn
 * @property Grunnkrets $grunnkrets
 * @property Stemmekrets $stemmekrets
 * @property Svalbardomrade $svalbardomrade
 */
class Adresse extends AbstractEntity {
	protected int $matrikkelenhetId;
	/** @var int[] */
	protected array $kretsIds;
	protected int $vegId;
	protected int $nummer;
	protected ?string $bokstav = null;
	protected Veg $veg;
	protected Matrikkelenhet $matrikkelenhet;
	protected Representasjonspunkt $representasjonspunkt;
	/** @var \Iaasen\Matrikkel\Entity\Bruksenhet[] */
	protected array $bruksenheter;

	protected ?Postnummeromrade $postnummeromrade;
	protected ?Tettsted $tettsted;
	protected ?Kirkesokn $kirkesokn;
	protected ?Grunnkrets $grunnkrets;
	protected ?Stemmekrets $stemmekrets;
	protected ?Svalbardomrade $svalbardomrade;
	protected ?Kommunalkrets $kommunalkrets;


	public function setMatrikkelenhetId($value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'matrikkelenhetId', $value);
	}


	public function setKretsIds(mixed $value) : void {
		if(is_object($value)) {
			$this->kretsIds = [];
			foreach($value->item AS $item) $this->kretsIds[] = $item->value;
		}
		else $this->setArrayInternal('kretsIds', $value);
	}


	public function setVegId(mixed $value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'vegId', $value);
	}


	public function addKrets(Krets $krets) : void {
		$propertyName = lcfirst(Krets::KRETSTYPER[$krets->kretstypeKodeId]);
		$this->$propertyName = $krets;
	}


	public function addBruksenhet(Bruksenhet $bruksenhet) : void {
		$this->bruksenheter[] = $bruksenhet;
	}

}
