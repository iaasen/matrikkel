<?php
/**
 * User: ingvar.aasen
 * Date: 22.05.2024
 */

namespace Iaasen\Matrikkel\LocalDb;

use Iaasen\Debug\Timer;
use SplFileObject;
use Symfony\Component\Console\Style\SymfonyStyle;
use ZipArchive;

class AdresseImportService {

    const CACHE_FOLDER = 'data/cache';
    const CSV_FILENAME = 'matrikkelenAdresseLeilighetsniva.csv';
    const ZIP_FILE = self::CACHE_FOLDER.'/matrikkel-address-import.zip';
    protected string $list;

    public static $settings = [
        'norge' => [
            'adresse_url' => 'https://nedlasting.geonorge.no/geonorge/Basisdata/MatrikkelenAdresse/CSV/Basisdata_0000_Norge_25833_MatrikkelenAdresse_CSV.zip',
            'extract_folder' => self::CACHE_FOLDER. '/Basisdata_0000_Norge_25833_MatrikkelenAdresse_CSV',
            'extract_filename' => 'matrikkelenAdresse.csv',
            'row_count' => 2589100,
        ],
        'norge_leilighetsnivaa' => [
            'adresse_url' => 'https://nedlasting.geonorge.no/geonorge/Basisdata/MatrikkelenAdresseLeilighetsniva/CSV/Basisdata_0000_Norge_25833_MatrikkelenAdresseLeilighetsniva_CSV.zip',
            'extract_folder' => self::CACHE_FOLDER. '/Basisdata_0000_Norge_25833_MatrikkelenAdresseLeilighetsniva_CSV',
            'extract_filename' => 'matrikkelenAdresseLeilighetsniva.csv',
            'row_count' => 3580100,
        ],
        'trondelag' => [
            'adresse_url' => 'https://nedlasting.geonorge.no/geonorge/Basisdata/MatrikkelenAdresse/CSV/Basisdata_50_Trondelag_25833_MatrikkelenAdresse_CSV.zip',
            'extract_folder' => self::CACHE_FOLDER. '/Basisdata_50_Trondelag_25833_MatrikkelenAdresse_CSV',
            'extract_filename' => 'matrikkelenAdresse.csv',
            'row_count' => 248900,
        ],
        'trondelag_leilighetsnivaa' => [
            'adresse_url' => 'https://nedlasting.geonorge.no/geonorge/Basisdata/MatrikkelenAdresseLeilighetsniva/CSV/Basisdata_50_Trondelag_25833_MatrikkelenAdresseLeilighetsniva_CSV.zip',
            'extract_folder' => self::CACHE_FOLDER . '/Basisdata_50_Trondelag_25833_MatrikkelenAdresseLeilighetsniva_CSV',
            'extract_filename' => 'matrikkelenAdresseLeilighetsniva.csv',
            'row_count' => 340900,
        ],
    ];

    public function __construct(
        protected AdresseTable $adresseTable,
        protected BruksenhetTable $bruksenhetTable,
    ) {}

    public function importAddresses(SymfonyStyle $io, string $list) : bool
    {
		Timer::setStart();
        $this->list = $list;

        $io->write('Download the import file... ');
		$success = $this->downloadFile($list);
		if(!$success) {
			$io->writeln('Failed to download file from ' . self::$settings[$list]['adresse_url']);
			return false;
		}
        $io->writeln('<info>Success</info>');

		$io->write('Extract the CSV file from the ZIP file... ');
        $success = $this->extractFile();
        if(!$success) {
            $io->writeln('Failed to extract the zip-file: '.self::ZIP_FILE);
            return false;
        }

		$fileObject = $this->openImportFile($list);
		$io->writeln('<info>Success</info>');

        // It takes very long time to count the number of lines in the file. Hardcoding an estimate instead
		//$fileLineCount = $this->countFileLines($fileObject);
        $fileLineCount = self::$settings[$list]['row_count'];

		$io->writeln('The file has about ' . $fileLineCount . ' lines');

		$io->writeln('Import addresses');
		$progressBar = $io->createProgressBar($fileLineCount);

        // Skip header row
        $fileObject->next();

		$count = 0;
		while($row = $fileObject->fgetcsv()) {
			if($row[2] == 'vegadresse') {
                if(str_contains($list, 'leilighetsnivaa')) {
                    $this->adresseTable->insertRowLeilighetsnivaa($row);
                    $this->bruksenhetTable->insertRow($row);
                }
                else {
                    $this->adresseTable->insertRow($row);
                }
            }
            $count++;
            if($count % 100 === 0) {
                $progressBar->setProgress($count);
            }
		}

        $progressBar->finish();
        $this->closeFile($fileObject);
        $this->deleteImportFiles($list);
        $io->writeln('');

        $this->adresseTable->flush();
        $this->bruksenhetTable->flush();

		$oldRows = $this->adresseTable->deleteOldRows();
		$io->writeln('');
		$io->writeln('Deleted ' . $oldRows . ' old address rows');

        if(str_contains($list, 'leilighetsnivaa')) {
            $oldRows = $this->bruksenhetTable->deleteOldRows();
            $io->writeln('Deleted ' . $oldRows . ' old bruksenhet rows');
        }
        else {
            $io->writeln('Truncated the bruksenhet table');
            $this->bruksenhetTable->truncateTable();
        }

        $io->writeln('');
		$io->writeln($count . ' street address rows imported');
		$io->writeln('The address table now contains ' . $this->adresseTable->countDbAddressRows() . ' addresses');
		$io->info('Completed in ' . round(Timer::getElapsed(), 3) . ' seconds');
		return true;
	}

	protected function downloadFile(string $list) : bool
    {
		return copy(self::$settings[$list]['adresse_url'], self::ZIP_FILE);
	}

    protected function extractFile(): bool
    {
        $zip = new ZipArchive();
        $zip->open(self::ZIP_FILE);
        $success = $zip->extractTo(self::CACHE_FOLDER);
        $zip->close();
        return $success;
    }

	protected function openImportFile(string $list) : SplFileObject
    {
		$fileObject = new SplFileObject(self::$settings[$list]['extract_folder'].'/'.self::$settings[$list]['extract_filename'], 'r');
		$fileObject->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY | SplFileObject::READ_CSV);
        $fileObject->setCsvControl(';');
		return $fileObject;
	}

	protected function closeFile(SplFileObject &$fileObject) : void
    {
		$fileObject = null;
	}

	protected function deleteImportFiles(string $list) : void
    {
		unlink(self::$settings[$list]['extract_folder'].'/'.self::$settings[$list]['extract_filename']);
        rmdir(self::$settings[$list]['extract_folder']);
        unlink(self::ZIP_FILE);
	}

	public function countFileLines(SplFileObject $fileObject) : int
    {
		$count = 0;
		while(!$fileObject->eof()) {
			$count++;
			$fileObject->next();
		}
		$fileObject->rewind();
		return $count;
	}

}
