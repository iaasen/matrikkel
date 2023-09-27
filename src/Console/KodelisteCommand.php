<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\Matrikkel\Console;

use Iaasen\Debug\Timer;
use Iaasen\Matrikkel\Service\KodelisteService;
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
		if($id) {
			$kodeliste = $this->kodelisteService->getKodeliste($id, true);
			$this->io->title($kodeliste->kodeIdType);
			$rows = [];
			foreach($kodeliste->koderIds AS $kode) {
				$rows[] = [$kode->id, $kode->navn[0]->value];
			}
			$this->io->table(['Kode', 'Navn'], $rows);

		}
		else {
			$kodelister = $this->kodelisteService->getKodelister();
			$rows = [];
			foreach($kodelister AS $kodeliste) {
				$rows[] = [$kodeliste->id, $kodeliste->kodeIdType];
			}
			$this->io->table(['Id', 'Type'], $rows);
		}

		$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');
		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('id', InputArgument::OPTIONAL);
	}

}
