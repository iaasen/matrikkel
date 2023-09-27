<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\DateTime;
use Iaasen\Model\AbstractEntityV2;

/**
 * @property int $id
 * @property DateTime $oppdateringsdato
 * @property int $versjonId
 * @property string $oppdatertAv
 * @property int $versjon
 */
abstract class AbstractEntity extends AbstractEntityV2 {
	protected int $id;
	protected DateTime $oppdateringsdato;
	protected int $versjonId;
	protected string $oppdatertAv;
	protected int $versjon;


	public function setId(mixed $value) {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'id', $value);
	}


	public function setOppdateringsdato(mixed $value) : void {
		if(is_object($value)) $value = $value->timestamp;
		$this->setObjectInternal(DateTime::class, 'oppdateringsdato', $value);
	}

}
