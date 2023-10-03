<?php
/**
 * User: ingvar.aasen
 * Date: 03.10.2023
 */

namespace Iaasen\Matrikkel\Entity\Matrikkelsok;

use Iaasen\Model\AbstractEntityV2;

/**
 * @property int $adressekode
 * @property int[] $husnummer
 */
class Veg extends AbstractMatrikkelsok {
	protected int $adressekode;
	/** @var int[] */
	protected array $husnummer;

	protected string $objekttype = 'VEG';
	protected string $kilde = 'ADRESSE';



	public function __set($name, $value) : void {
		if($name == 'HUSNUMMER') {
			$this->husnummer = [];
			$numbers = explode(',', $value);
			foreach($numbers AS $number) $this->husnummer[] = trim($number);
		}
		else parent::__set($name, $value);
	}

}
