<?php
/**
 * User: ingvar.aasen
 * Date: 22.05.2024
 */

namespace Iaasen\Matrikkel\LocalDb;

use Iaasen\DateTime;
use Iaasen\Debug\Timer;
use Laminas\Db\Adapter\Adapter;
use SplFileObject;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

class AdresseImportService {
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
	const TABLE_NAME = 'matrikkel_addresses';

	// Temp storage
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
		$fileObject = $this->openImportFile();
		$io->writeln('Success');

		// This command will rewind the file to first row.
		// The next fgetcsv-call will fetch the second line and skip the column names.
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

		$oldRows = $this->deleteOldRows();
		$io->writeln('');
		$io->writeln('Deleted ' . $oldRows . ' old rows');

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


	protected function openImportFile() : SplFileObject {
		$fileObject = new SplFileObject(self::CSV_FILE, 'r');
		$fileObject->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
		return $fileObject;
	}


	protected function closeFile(SplFileObject &$fileObject) : void {
		$fileObject = null;
	}


	protected function deleteImportFile() : void {
		unlink(self::CSV_FILE);
	}


	public function insertRow(array $row) : void {
		// Fields from the matrikkel-sok that doesn't exist in this csv import:
	 	// tittel, fylkesnavn, kilde

		$this->addressRows[] = [
			'adresseId' => (int) $row[32],
			'kommunenummer' => (int) $row[1],
			'kommunenavn' => $row[2],
			'adressetype' => $row[3],
			'adressekode' => $row[6],
			'adressenavn' => $row[7],
			'nummer' => (int) $row[8],
			'bokstav' => $row[9],
			'gardsnummer' => (int) $row[10],
			'bruksnummer' => (int) $row[11],
			'festenummer' => (int) $row[12],
			'undernummer' => (int) $row[13],
			'adresseTekst' => $row[14],
			'epsg' => (int) $row[16],
			'nord' => (float) $row[17],
			'øst' => (float) $row[18],
			'postnummer' => (int) $row[19],
			'poststed' => $row[20],
			'grunnkretsnavn' => $row[22],
			'soknenavn' => $row[24],
			'tettstednavn' => $row[27],
			'fylkesnummer' => floor((int) $row[1] / 100),
			'search_context' => $row[7] . ' ' . $row[8] . $row[9] . ' ' . $row[19] . ' ' . $row[20] . ' ' . $row[27] . ' ' . $row[2],
		];
	}


	public function flush() : void {
		if(!count($this->addressRows)) return;

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
		$columnNames = array_keys(current($this->addressRows));
		$columnsString = array_map(function ($column) { return '`' . $column . '`'; }, $columnNames);
		$columnsString = implode(',', $columnsString);
		$columnsString = '(' . $columnsString . ')';
		return 'REPLACE INTO ' . self::TABLE_NAME . ' ' . $columnsString . PHP_EOL . 'VALUES' . PHP_EOL;
	}


	public function deleteOldRows() : int {
		$date = new DateTime();
		$date->modify('-3 hour'); // Go back 3 hours to get before UTC in case of timezone errors
		$sql = 'DELETE FROM ' . self::TABLE_NAME . ' WHERE timestamp_created < "' . $date->formatMysql() . '";';
		$result = $this->dbAdapter->query($sql)->execute();
		return $result->getAffectedRows();
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
