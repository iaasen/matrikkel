<?php
/**
 * User: ingvar.aasen
 * Date: 13.09.2023
 */

namespace Iaasen\MatrikkelApi\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:ping', description: 'Test the SOAP-connection')]
class PingCommand extends AbstractCommand {

	public function execute(InputInterface $input, OutputInterface $output) : int {
		$output->write('ddd');
		//$this->io->write('aaa');
		return Command::SUCCESS;
	}

	protected function configure(): void {
		$this->setHelp('aaa');
	}
}