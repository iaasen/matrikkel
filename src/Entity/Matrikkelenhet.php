<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\MatrikkelApi\Entity;

/**
 * @property Matrikkelnummer $matrikkelnummer
 * @property string $bruksnavn
 */
class Matrikkelenhet extends AbstractEntity {
	protected Matrikkelnummer $matrikkelnummer;
	protected string $bruksnavn;
}