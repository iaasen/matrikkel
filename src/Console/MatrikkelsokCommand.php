<?php
/**
 * User: ingvar.aasen
 * Date: 03.10.2023
 */

namespace Iaasen\Matrikkel\Console;

use Iaasen\Debug\Timer;
use Iaasen\Matrikkel\Entity\Matrikkelsok\Eiendom;
use Iaasen\Matrikkel\Entity\Matrikkelsok\Veg;
use Iaasen\Matrikkel\Entity\Matrikkelsok\Vegadresse;
use Iaasen\Matrikkel\Service\MatrikkelsokService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:sok', description: 'Matrikkel search')]
class MatrikkelsokCommand extends AbstractCommand {

	public function __construct(
		protected MatrikkelsokService $matrikkelsokService,
	) {
		parent::__construct('matrikkel:sok');
	}


	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('MatrikkelAPI Matrikkelsøk');
		Timer::setStart();

		$search = $input->getArgument('search');
		$addresses = $this->matrikkelsokService->searchAddresses($search);

		// Veg
		$rows = [];
		foreach($addresses AS $address) {
			if($address instanceof Veg)
				$rows[] = [$address->id, $address->navn, $address->kommunenr . ' ' . $address->kommunenavn, implode(', ', $address->husnummer)];
		}
		$this->io->title('Veg');
		$this->io->table(['Id', 'Navn', 'Kommune', 'Husnummer'], $rows);

		// Vegadresse
		$rows = [];
		foreach($addresses AS $address) {
			if($address instanceof Vegadresse)
				$rows[] = [$address->id, $address->adressenavn, $address->husnr . $address->bokstav, $address->postnr . ' ' . $address->poststed];
		}
		$this->io->title('Vegadresse');
		$this->io->table(['AdresseId', 'Veg', 'Nummer', 'Poststed'], $rows);

		// Eiendom
		$rows = [];
		foreach($addresses AS $address) {
			if($address instanceof Eiendom) {
				$rows[] = [$address->id, $address->navn, $address->vegadresse, $address->kommunenr . ' ' . $address->kommunenavn];
			}
		}
		$this->io->title('Eiendom');
		$this->io->table(['Id', 'Navn', 'Vegadresse', 'Kommune'], $rows);

		$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');
		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('search', InputArgument::REQUIRED);
	}

}
