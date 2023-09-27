<?php
/**
 * User: ingvar.aasen
 * Date: 19.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\Model\AbstractEntityV2;

/**
 * @property int $id
 * @property string $kodeverdi
 * @property Kodenavn[] $navn
 */
class Kode extends AbstractEntityV2 {
	protected int $id;
	protected string $kodeverdi;
	/** @var \Iaasen\Matrikkel\Entity\KodeNavn[] */
	protected array $navn;


	public function setId(object|int $id) : void {
		if(is_object($id)) {
			$this->id = $id->value;
		}
		else $this->id = $id;
	}


	public function setNavn(object $object) : void {
		if(is_object($object->entry)) {
			$this->navn = [new KodeNavn([
				'key' => $object->entry->key,
				'value' => $object->entry->value,
			])];
		}
		else { // array
			foreach($object->entry as $value) {
				$this->navn[] = new KodeNavn([
					'key' => $value->key,
					'value' => $value->value,
				]);
			}
		}
	}

}
