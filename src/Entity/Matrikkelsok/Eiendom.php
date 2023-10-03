<?php
/**
 * User: ingvar.aasen
 * Date: 03.10.2023
 */

namespace Iaasen\Matrikkel\Entity\Matrikkelsok;

use Iaasen\Model\AbstractEntityV2;

/**
 * @property string $tittel2
 * @property string $tittel3
 * @property string $tittel4
 * @property int $gardsnr
 * @property int $bruksnr
 * @property int $festenr
 * @property int $seksjonsnr
 * @property string $vegadresse
 * @property string $vegadresse2
 */
class Eiendom extends AbstractMatrikkelsok {
	protected string $tittel2;
	protected string $tittel3;
	protected string $tittel4;
	protected int $gardsnr;
	protected int $bruksnr;
	protected int $festenr;
	protected int $seksjonsnr;
	protected string $vegadresse;
	protected string $vegadresse2;

	protected string $objekttype = 'EIENDOM';
	protected string $kilde = 'EIENDOM';


}
