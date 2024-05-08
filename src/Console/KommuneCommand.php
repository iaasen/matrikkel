<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Console;

use Iaasen\Debug\Timer;
use Iaasen\Matrikkel\Service\KommuneService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:kommune', description: 'Kommuner')]
class KommuneCommand extends AbstractCommand {

	public function __construct(
		protected KommuneService $kommuneService,
	) {
		parent::__construct('matrikkel:kommune');
	}


	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('MatrikkelAPI Kommune');
		Timer::setStart();

		$id = $input->getArgument('id');
		$kommune = $this->kommuneService->getKommuneById($id);
		dump($kommune);

		$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');
		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('id', InputArgument::REQUIRED);
	}

}
