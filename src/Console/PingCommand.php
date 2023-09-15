<?php
/**
 * User: ingvar.aasen
 * Date: 13.09.2023
 */

namespace Iaasen\MatrikkelApi\Console;

use Iaasen\MatrikkelApi\Service\KommuneService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:ping', description: 'Test the SOAP-connection')]
class PingCommand extends AbstractCommand {

	public function __construct(
		protected KommuneService  $kommuneService,
	) {
		parent::__construct();
	}


	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('MatrikkelAPI ping');
		try {
			$this->kommuneService->getKommuneById(500611);
		}
		catch (\Exception $e) {
			$this->io->error($e->getCode() . ' : ' . $e->getMessage());
			$this->io->error('No success');
			return Command::FAILURE;
		}
		$this->io->success('Success');
		return Command::SUCCESS;
	}

}
