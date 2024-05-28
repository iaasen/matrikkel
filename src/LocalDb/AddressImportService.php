<?php
/**
 * User: ingvar.aasen
 * Date: 22.05.2024
 */

namespace Iaasen\Matrikkel\LocalDb;

use Iaasen\Debug\Timer;
use Laminas\Db\Adapter\Adapter;
use SplFileObject;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

class AddressImportService {
	// All of Norway
	const ADDRESS_URL = 'https://nedlasting.geonorge.no/geonorge/Basisdata/MatrikkelenAdresse/CSV/Basisdata_0000_Norge_25833_MatrikkelenAdresse_CSV.zip';
	const EXTRACT_FOLDER = self::CACHE_FOLDER . '/Basisdata_0000_Norge_25833_MatrikkelenAdresse_CSV';

	// Trøndelag
	//const ADDRESS_URL = 'https://nedlasting.geonorge.no/geonorge/Basisdata/MatrikkelenAdresse/CSV/Basisdata_50_Trondelag_25833_MatrikkelenAdresse_CSV.zip';
	//const EXTRACT_FOLDER = self::CACHE_FOLDER . '/Basisdata_50_Trondelag_25833_MatrikkelenAdresse_CSV';

	// Same for all
	const CACHE_FOLDER = 'data/cache';
	const ZIP_FILE = self::CACHE_FOLDER . '/matrikkel-address-import.zip';
	const CSV_FILE = self::EXTRACT_FOLDER . '/matrikkelenAdresse.csv';

	// Only these columns will be imported
	const ADDRESS_COLUMNS = [
		'adresseId',
		'kommunenummer',
		'kommunenavn',
	];
	const TABLE_NAME = 'matrikkel_addresses';

	// Temp storage
	protected array $columnNames;
	protected array $addressRows = [];


	public function __construct(
		protected Adapter $dbAdapter
	) {}


	public function importAddresses(SymfonyStyle $io) : bool {
		Timer::setStart();

		$success = $this->downloadFile();
		if(!$success) {
			$io->writeln('Failed to download file from: ' . self::ADDRESS_URL);
			return false;
		}

		$io->write('Extract the CSV file from the ZIP file: ');
		$fileObject = $this->openFile();
		$io->writeln('Success');

		$io->write('Collect column names: ');
		$this->setColumnNames($fileObject);
		$io->writeln(implode(', ', $this->columnNames));

		$fileLineCount = $this->countFileLines($fileObject);
		$io->writeln('The file has ' . $fileLineCount . ' lines');

		$io->writeln('Import addresses');
		$progressBar = $io->createProgressBar($fileLineCount);

		$count = 0;
		while($row = $fileObject->fgetcsv(separator: ';')) {
			if($row[3] == 'vegadresse') $this->insertRow($row);
			if(count($this->addressRows) >= 100) {
				$count += 100;
				$this->flush();
				$progressBar->advance(100);
			}
		}

		$count += count($this->addressRows);
		$this->flush();
		$progressBar->finish();
		$this->closeFile($fileObject);

		$io->writeln('');
		$io->writeln($count . ' road addresses imported');
		$io->writeln('The address table now contains ' . $this->countDbAddressRows() . ' addresses');
		$io->info('Completed in ' . round(Timer::getElapsed(), 3) . ' seconds');
		return true;
	}


	protected function downloadFile() : bool {
		$success = copy(self::ADDRESS_URL, self::ZIP_FILE);
		if(!$success) return false;

		$zip = new ZipArchive();
		$zip->open(self::ZIP_FILE);
		$success = $zip->extractTo(self::CACHE_FOLDER);
		$zip->close();
		return $success;
	}


	protected function openFile() : SplFileObject {
		$fileObject = new SplFileObject(self::CSV_FILE, 'r');
		$fileObject->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
		return $fileObject;
	}


	protected function closeFile(SplFileObject &$fileObject) : void {
		$fileObject = null;
	}


	protected function setColumnNames(SplFileObject $fileObject) : void {
		$columnNames = $fileObject->fgetcsv(separator:';');
		$columnNames[0] = ltrim($columnNames[0], "\u{FEFF}");
		$this->columnNames = array_intersect($columnNames, self::ADDRESS_COLUMNS);
	}


	public function insertRow(array $row) : void {
		$row = array_intersect_key($row, $this->columnNames);
		$this->addressRows[] = $row;
	}


	public function flush() : void {
		$sql = $this->getStartInsert();
		$valueRows = [];
		foreach($this->addressRows as $addressRow) {
			foreach($addressRow AS $key => $column) {
				$addressRow[$key] = '"' . $column . '"';
			}
			$valueRows[] .= '(' . implode(',', $addressRow) . ')';
		}
		$sql .= implode(",\n", $valueRows);
		$sql .= ';';
		$this->dbAdapter->query($sql)->execute();
		$this->addressRows = [];
	}


	public function getStartInsert() : string {
		$columnsString = array_map(function ($column) { return '`' . $column . '`'; }, $this->columnNames);
		$columnsString = implode(',', $columnsString);
		$columnsString = '(' . $columnsString . ')';
		return 'REPLACE INTO ' . self::TABLE_NAME . ' ' . $columnsString . PHP_EOL . 'VALUES' . PHP_EOL;
	}


	public function countFileLines(SplFileObject $fileObject) : int {
		$count = 0;
		while(!$fileObject->eof()) {
			$count++;
			$fileObject->next();
		}
		$fileObject->rewind();
		return $count;
	}


	public function countDbAddressRows() : int {
		$sql = 'SELECT COUNT(*) FROM ' . self::TABLE_NAME . ';';
		$result = $this->dbAdapter->query($sql)->execute();
		return current($result->current());
	}

}
