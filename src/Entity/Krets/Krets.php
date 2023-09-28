<?php
/**
 * User: ingvar.aasen
 * Date: 28.09.2023
 */

namespace Iaasen\Matrikkel\Entity\Krets;

use Iaasen\Matrikkel\Entity\AbstractEntity;

/**
 * @property int $kretsnummer
 * @property string $kretsnavn
 * @property int $kretstypeKodeId
 * @property int $kretsflateId
 */
class Krets extends AbstractEntity {
	protected int $kretsnummer;
	protected string $kretsnavn;
	protected int $kretstypeKodeId;
	protected int $kretsflateId;


	public function setKretstypeKodeId(mixed $value) {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'kretstypeKodeId', $value);
	}


	public function setKretsflateId(mixed $value) {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'kretsflateId', $value);
	}


	const KRETSTYPER = [
		4097 => 'Postnummeromrade',
		4098 => 'Tettsted',
		4099 => 'Kirkesokn',
		4100 => 'Grunnkrets',
		4101 => 'Stemmekrets',
		4102 => 'Svalbardomrade',
	];

}
