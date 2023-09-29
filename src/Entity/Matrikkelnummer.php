<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

use Iaasen\Model\AbstractEntityV2;

/**
 * https://www.kartverket.no/eiendom/adressering/finn-gards--og-bruksnummer
 * Matrikkel is listed in this order: kommuneId-gardsnummer/bruksnummer/festenummer/seksjonsnummer-undernummer
 * @property int $kommuneId
 * @property int $gardsnummer
 * @property int $bruksnummer
 * @property int $festenummer
 * @property int $seksjonsnummer
 * @property Kommune $kommune
 */
class Matrikkelnummer extends AbstractEntityV2 {
	protected int $kommuneId;
	protected int $gardsnummer;
	protected int $bruksnummer;
	protected int $festenummer; // Festenummer tildeles når en del av en vanlig grunneiendom leies bort (festes)
	protected int $seksjonsnummer; // Seksjonsnummer tildeles når en eiendom seksjoneres, slik at hver eierseksjon får et eget seksjonsnummer
	protected Kommune $kommune;


	public function setKommuneId($value) : void {
		if(is_object($value)) $value = $value->value;
		$this->setPrimitiveInternal('int', 'kommuneId', $value);
	}

}
