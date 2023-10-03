<?php
/**
 * User: ingvar.aasen
 * Date: 03.10.2023
 */

namespace Iaasen\Matrikkel\Entity\Matrikkelsok;

use Iaasen\Model\AbstractEntityV2;

/**
 * @property int $adressekode
 * @property string $adressenavn
 * @property int $husnr
 * @property string $bokstav
 * @property string $matrikkelnr
 * @property int $postnr
 * @property string $poststed
 */
class Vegadresse extends AbstractMatrikkelsok {
	protected int $adressekode;
	protected string $adressenavn;
	protected int $husnr;
	protected ?string $bokstav = null;
	protected string $matrikkelnr;
	protected int $postnr;
	protected string $poststed;

	protected string $objekttype = 'VEGADRESSE';
	protected string $kilde = 'ADRESSE';


}
