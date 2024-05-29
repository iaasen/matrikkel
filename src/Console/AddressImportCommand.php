<?php
/**
 * User: ingvar.aasen
 * Date: 29.04.2024
 */

namespace Iaasen\Matrikkel\Console;

use Iaasen\Matrikkel\LocalDb\AdresseImportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'matrikkel:adresse-import', description: 'Importer adresser til lokal db')]
class AddressImportCommand extends AbstractCommand {

	public function __construct(
		protected AdresseImportService $addressImportService,
	) {
		parent::__construct();
	}

	public function execute(InputInterface $input, OutputInterface $output) : int {
		$this->io->title('Importer adresser');
		$success = $this->addressImportService->importAddresses($this->io);
		if($success) {
			$this->io->success('Success');
			return 0;
		}
		$this->io->error('Failed');
		return 1;
	}

}
