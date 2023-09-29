<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

/**
 * @property Matrikkelnummer $matrikkelnummer
 * @property string $bruksnavn
 */
class Matrikkelenhet extends AbstractEntity {
	protected string $bruksnavn;
	protected Matrikkelnummer $matrikkelnummer;
}