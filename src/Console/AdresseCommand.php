<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\MatrikkelApi\Console;

use Iaasen\Debug\Timer;
use Iaasen\MatrikkelApi\Service\AdresseService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
			//$address = $this->adresseService->searchAddress('eggevegen');
			$address = $this->adresseService->getAddressByAddressId($id);
			dump($address);
		}
		else {
			// TODO
		}

		$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');
		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('id', InputArgument::OPTIONAL);
	}
}