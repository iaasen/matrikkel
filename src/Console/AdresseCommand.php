<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Console;

use Iaasen\Debug\Timer;
use Iaasen\Matrikkel\Service\AdresseService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:adresse', description: 'Addresses')]
class AdresseCommand extends AbstractCommand {

	public function __construct(
		protected AdresseService $adresseService,
	) {
		parent::__construct('matrikkel:adresse');
	}


	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('MatrikkelAPI Adresse');
		Timer::setStart();

		$id = $input->getArgument('id');
		if($id) {
			$address = $this->adresseService->getAddressById($id);
			dump($address);
		}
		else {
			if($search = $input->getOption('search')) {
				$addresses = $this->adresseService->searchAddress($search);
				$rows = [];
				foreach($addresses AS $address) {
					$rows[] = [$address->id, $address->veg->adressenavn, $address->nummer . $address->bokstav, $address->postnummeromrade->getPostnummer() . ' ' . $address->postnummeromrade->kretsnavn];
				}
				$this->io->table(['AdresseId', 'Veg', 'Nummer', 'Poststed'], $rows);
			}
		}

		$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');
		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('id', InputArgument::OPTIONAL);
		$this->addOption('search', 's', InputOption::VALUE_OPTIONAL, 'Fuzzy search');
	}
}