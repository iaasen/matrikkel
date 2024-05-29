<?php
/**
 * User: ingvar.aasen
 * Date: 03.10.2023
 */

namespace Iaasen\Matrikkel\Entity\Matrikkelsok;

use Iaasen\Model\AbstractEntityV2;

/**
 * @property int $id
 * @property string $tittel
 * @property string $navn
 * @property string $tilhoerighet
 * @property string $kommunenr
 * @property string $kommunenavn
 * @property float $latitude
 * @property float $longitude
 * @property string $fylkesnr
 * @property string $fylkesnavn
 * @property string $objekttype
 * @property string $kilde
 */
class AbstractMatrikkelsok extends AbstractEntityV2 {
	protected int $id;
	protected string $tittel;
	protected string $navn;
	protected string $tilhoerighet;
	protected string $kommunenr;
	protected string $kommunenavn;

	// From MatrikkelApi - UTM zone 32 (25832)
	// From CSV import - UTM zone 33 (25833)
	protected float $latitude; // North
	protected float $longitude; // East

	protected string $fylkesnr;
	protected string $fylkesnavn;
	protected string $objekttype;
	protected string $kilde;

	public function __set($name, $value) : void {
		if(in_array($name, self::SEARCH_RESULT_FIELD_NAMES)) {
			parent::__set(strtolower($name), $value);
		}
		else parent::__set($name, $value);
	}


	const SEARCH_RESULT_FIELD_NAMES = [
		'ID',
		'TITTEL',
		'TITTEL2',
		'TITTEL3',
		'TITTEL4',
		'NAVN',
		'TILHOERIGHET',
		'ADRESSEKODE',
		'ADRESSENAVN',
		'HUSNR',
		'BOKSTAV',
		'KOMMUNENR',
		'KOMMUNENAVN',
		'LATITUDE',
		'LONGITUDE',
		'MATRIKKELNR',
		'POSTNR',
		'POSTSTED',
		'FYLKESNR',
		'FYLKESNAVN',
		'OBJEKTTYPE',
		'KILDE',
		'HUSNUMMER',
		'GARDSNR',
		'BRUKSNR',
		'FESTENR',
		'SEKSJONSNR',
		'VEGADRESSE',
		'VEGADRESSE2',
	];

	const OBJEKTTYPE_OPTIONS = [
		'VEG',
		'VEGADRESSE',
		'BOLIG',
		'BYGNING',
	];




}