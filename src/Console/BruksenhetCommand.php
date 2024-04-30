<?php
/**
 * User: ingvar.aasen
 * Date: 29.04.2024
 */

namespace Iaasen\Matrikkel\Console;

use Iaasen\Debug\Timer;
use Iaasen\Matrikkel\Service\AdresseService;
use Iaasen\Matrikkel\Service\BruksenhetService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:bruksenhet', description: 'Bruksenheter')]
class BruksenhetCommand extends AbstractCommand {

	public function __construct(
		protected BruksenhetService $bruksenhetService,
		protected AdresseService $adresseService,
	) {
		parent::__construct('matrikkel:bruksenhet');
	}


	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('MatrikkelAPI Bruksenhet');

		$id = $input->getArgument('id');
		if($id) {
			Timer::setStart();
			$bruksenhet = $this->bruksenhetService->getBruksenhetById($id);
			$this->io->writeln('Bruksenhet id: ' . $bruksenhet->id);
			$this->io->writeln('Bolignummer: ' . $bruksenhet->getBruksenhetsnummer());
			$this->io->writeln('');
			$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');

		}
		else {
			if($addressId = $input->getOption('adresseid')) {
			 	Timer::setStart();
				$address = $this->adresseService->getAddressById($addressId);
				$getAddress = Timer::getElapsed();
				$bruksenheter = $this->bruksenhetService->getBruksenheterByAdresseId($addressId);
				$getBruksenheter = Timer::getElapsed();

				$rows = [
					[
						'Adresse',
						$address->id,
						$address->veg->adressenavn . ' ' . $address->nummer . ', ' . $address->postnummeromrade->getPostnummer() . ' ' . $address->postnummeromrade->kretsnavn
					]
				];
				foreach($bruksenheter AS $bruksenhet) {
					$rows[] = ['Bruksenhet', $bruksenhet->id, $bruksenhet->getBruksenhetsnummer()];
				}

				$this->io->table(
					[
						'Type',
						'Id',
						'Navn',
					],
					$rows,
				);
				$this->io->writeln('<info>Execution time adresse: ' . $getAddress . '</info>');
				$this->io->writeln('<info>Execution time bruksenheter: ' . $getBruksenheter . '</info>');
			}
		}

		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('id', InputArgument::OPTIONAL);
		$this->addOption('adresseid', 'a', InputOption::VALUE_OPTIONAL, 'Lookup all bruksenheter of given addresse id');
	}

}
