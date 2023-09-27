<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\DateTime;
use Iaasen\Model\AbstractEntityV2;


/**
 * TODO Not complete. Finish it when actually needed
 */
class Kommune extends AbstractEntityV2 {
	protected int $id;
	protected DateTime $oppdateringsdato;
	protected int $versjonId;
	protected string $oppdatertAv;
	protected int $versjon;
	protected int $kommunenummer;
	protected string $kommunenavn;
	protected int $fylkeId;









	public function setId(object|int $id) : void {
		if(is_object($id)) {
			$this->id = $id->value;
		}
		else $this->id = $id;
	}


	public function setOppdateringsdato(\stdClass|DateTime $dato) : void {
		if($dato instanceof DateTime) $this->oppdateringsdato = $dato;
		else $this->oppdateringsdato = new DateTime($dato->timestamp);
	}


	public function setFylkeId(int|object $id) : void {
		if(is_object($id)) $this->fylkeId = $id->value;
		else $this->fylkeId = $id;
	}

}