<?php
/**
 * User: ingvar.aasen
 * Date: 28.09.2023
 */

namespace Iaasen\Matrikkel\Entity\Krets;

class Postnummeromrade extends Krets {

	// Get postnummer with 4 digits. E.g. 0371
	public function getPostnummer() : string {
		return str_repeat('0', 4 - strlen($this->kretsnummer)). $this->kretsnummer;
	}

}
