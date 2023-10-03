<?php
/**
 * User: ingvar.aasen
 * Date: 29.09.2023
 */

namespace Iaasen\Matrikkel\Entity;

class Bruksenhet extends AbstractEntity {
	protected int $byggId;
	protected int $etasjeplanKodeId;
	protected int $etasjenummer;
	protected int $lopenummer;
	protected int $adresseId;
	protected int $matrikkelenhetId;
	protected int $bruksenhetstypeKodeId;
	protected bool $byggSkjermingsverdig;

	const ETASJEPLAN_KODER = [
		0 => '',
		1 => 'H', // Hovedetasje
		2 => 'K', // Kjelleretasje
		3 => 'L', // Loft
		4 => 'U', // Underetasje
	];

	const BRUKSENHETSTYPE_KODER = [
		0 => 'B', // Bolig
		1 => 'I', // Ikke godkjent bolig
		2 => 'F', // Fritidsbolig
		3 => 'A', // Annet enn bolig
		4 => 'U', // Unummerert bruksenhet
		5 => 'X', //
	];

	public function setByggId(mixed $value) : void { $this->setValueObjectInternal('byggId', $value); }
	public function setEtasjeplanKodeId(mixed $value) : void { $this->setValueObjectInternal('etasjeplanKodeId', $value); }
	public function setAdresseId(mixed $value) : void { $this->setValueObjectInternal('adresseId', $value); }
	public function setMatrikkelenhetId(mixed $value) : void { $this->setValueObjectInternal('matrikkelenhetId', $value); }
	public function setBruksenhetstypeKodeId(mixed $value) : void { $this->setValueObjectInternal('bruksenhetstypeKodeId', $value); }


	public function getBruksenhetsnummer() : string {
		return
			self::ETASJEPLAN_KODER[$this->etasjeplanKodeId] .
			str_repeat('0', 2 - strlen($this->etasjenummer)) . $this->etasjenummer .
			str_repeat('0', 2 - strlen($this->lopenummer)) . $this->lopenummer;
	}


	//	protected int $antallRom; // Not included in "berettiget interesse"
	//	protected int $antallBad; // Not included in "berettiget interesse"
	//	protected int $antallWC; // Not included in "berettiget interesse"
	//	protected int $bruksAreal; // Not included in "berettiget interesse"
	//	protected int $kjokkentilgangId; // Not included in "berettiget interesse"
	//	protected bool $skalUtga; // Not included in "berettiget interesse"
	//	protected bool $kostraFunksjonsKodeId; // Not included in "berettiget interesse"
	//	protected bool $kostraLeieareal; // Not included in "berettiget interesse"

}