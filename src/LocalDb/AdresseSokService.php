<?php
/**
 * User: ingvar.aasen
 * Date: 29.05.2024
 */

namespace Iaasen\Matrikkel\LocalDb;

use Iaasen\Matrikkel\Entity\Matrikkelsok\Vegadresse;
use Laminas\Db\Adapter\Adapter;

class AdresseSokService {

	public function __construct(
		protected Adapter $dbAdapter
	) {}


	/**
	 * @param string $search
	 * @return Vegadresse[]
	 */
	public function search(string $search): array {
		$sql = 'SELECT * FROM ' . AdresseImportService::TABLE_NAME . " WHERE adresseTekst LIKE CONCAT('%', ?, '%') LIMIT 20;";
		$request = $this->dbAdapter->query($sql);

		$result = $request->execute([$search]);
		$addresses = [];
		foreach ($result as $row) {
			$addresses[] = self::createMatrikkelSokObject($row);
		}
		return $addresses;
	}


	public static function createMatrikkelSokObject(array $row) : Vegadresse {
		return new Vegadresse([
			'id' => $row['adresseId'],
			'tittel' => $row['adresseTekst'] . ', ' . $row['poststed'],
			'navn' => $row['adresseTekst'],
			'tilhoerighet' => implode(', ', [
				$row['poststed'],
				$row['tettstednavn'],
				$row['kommunenavn'],
				$row['soknenavn'],
			]),
			'kommunenr' => $row['kommunenummer'],
			'kommunenavn' => $row['kommunenavn'],
			'epsg' => $row['epsg'],
			'latitude' => $row['nord'],
			'longitude' => $row['øst'],
			'fylkesnr' => floor($row['kommunenummer']/100),
			'fylkesnavn' => '', // Missing from csv
			// 'objekttype' => '',
			// 'kilde' => '',
			'adressekode' => $row['adressekode'],
			'adressenavn' => $row['adressenavn'],
			'husnr' => $row['nummer'],
			'bokstav' => $row['bokstav'],
			'matrikkelnr' => implode('/', [
				$row['gardsnummer'], $row['bruksnummer'], $row['festenummer'], $row['undernummer']	,
			]),
			'postnr' => $row['postnummer'],
			'poststed' => $row['poststed'],
		]);
	}

}
