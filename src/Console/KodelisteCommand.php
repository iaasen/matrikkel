<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\MatrikkelApi\Console;

use Iaasen\Debug\Timer;
use Iaasen\MatrikkelApi\Service\KodelisteService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:kodeliste', description: 'Option lists')]
class KodelisteCommand extends AbstractCommand {

	public function __construct(
		protected KodelisteService $kodelisteService
	) {
		parent::__construct();
	}


	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('MatrikkelAPI Kodeliste');
		Timer::setStart();

		$id = $input->getArgument('id');
		if($id) dump($this->kodelisteService->getKodeliste($id, true));
		else dump($this->kodelisteService->getKodelister());

		$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');
		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('id', InputArgument::OPTIONAL);
	}

}
