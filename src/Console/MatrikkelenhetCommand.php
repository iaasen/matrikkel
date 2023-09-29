<?php
/**
 * User: ingvar.aasen
 * Date: 22.09.2023
 */

namespace Iaasen\Matrikkel\Console;

use Iaasen\Debug\Timer;
use Iaasen\Exception\InvalidArgumentException;
use Iaasen\Matrikkel\Service\AdresseService;
use Iaasen\Matrikkel\Service\MatrikkelenhetService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:matrikkelenhet', description: 'Matrikkelenhet')]
class MatrikkelenhetCommand extends AbstractCommand {

	public function __construct(
		protected MatrikkelenhetService $matrikkelenhetService,
		protected AdresseService $adresseService,
	) {
		parent::__construct('matrikkel:matrikkelenhet');
	}


	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('MatrikkelAPI Matrikkelenhet');
		Timer::setStart();

		$id = $input->getArgument('id');
		if($id) {
			$matrikkelenhet = $this->matrikkelenhetService->getMatrikkelenhetById($id);
			dump($matrikkelenhet);
		}
		else {
			if($matrikkelNumber = $input->getOption('matrikkel')) {
				$matrikkelPattern = '/^(\d{4})-(\d{1,4})\/(\d{1,4})(\/?(\d{1,4}))?(\/(\d{1,4}))?$/';
				$match = [];
				$isAMatch = preg_match($matrikkelPattern, $matrikkelNumber, $match);
				if(!$isAMatch) throw new InvalidArgumentException('Not a valid matrikkel number. Format: knr-gnr/bnr[/fnr[/snr]]');
				$matrikkelArray = [
					(int)$match[1],
					(int)$match[2],
					(int)$match[3],
					isset($match[5]) ? (int) $match[5] : 0,
					isset($match[7]) ? (int) $match[7] : 0,
				];

				$result = call_user_func_array(array($this->matrikkelenhetService, "getMatrikkelenhetByMatrikkel"), $matrikkelArray);
				dump($result);
			}
		}

		$this->io->writeln('<info>Execution time: ' . Timer::getElapsed() . '</info>');
		return Command::SUCCESS;
	}


	public function configure() : void {
		$this->addArgument('id', InputArgument::OPTIONAL);
		$this->addOption('matrikkel', 'm', InputOption::VALUE_OPTIONAL, 'Format: knr-gnr/bnr[/fnr[/snr]]');
	}

}
