<?php
/**
 * User: ingvar.aasen
 * Date: 29.05.2024
 */

namespace Iaasen\Matrikkel\LocalDb;

use Iaasen\DateTime;
use Iaasen\Matrikkel\Entity\Matrikkelsok\Vegadresse;
use Laminas\Db\Adapter\Adapter;

class AdresseSokService {
    protected string $tableName = 'matrikkel_adresser';

	public function __construct(
		protected Adapter $dbAdapter
	) {}


	/**
	 * @param string $search
	 * @return Vegadresse[]
	 */
	public function search(string $search): array {
		if(!strlen($search)) return [];

		// Prepare search fields
		$searchContext = preg_split("/[, ]/", $search, -1, PREG_SPLIT_NO_EMPTY);
		$streetName = null;
		$postalCode = null;

		if(preg_match('/\d{4}/', reset($searchContext))) {
			$postalCode = reset($searchContext);
			array_shift($searchContext);
		}
//		$contextCount = count($searchContext);
//		for($i=0; $i < $contextCount; $i++) {
//			$field = array_shift($searchContext);
//			if(preg_match('/\d{4}/', $field)) {
//				$postalCode = $field;
//			}
//			else array_push($searchContext, $field);
//		}
		if(count($searchContext)) {
			$streetName = array_shift($searchContext);
			$streetName = str_replace(['veg', 'vei'], 've_', $streetName);
		}

		// Prepare where search
		$where = [];
		$parameters = [];
		if($streetName) {
			$where[] = "adressenavn LIKE CONCAT(?, '%')";
			$parameters[] = $streetName;
		}
		if($postalCode) {
			$where[] = "postnummer = ?";
			$parameters[] = $postalCode;
		}
		foreach($searchContext AS $context) {
			$context = str_replace(['veg', 'vei'], 've_', $context);
			$where[] = "search_context LIKE CONCAT('%', ?, '%')";
			$parameters[] = $context;
		}

		// Create the query
		$table = $this->tableName;
		$sql = <<<EOT
		SELECT *
		FROM $table
		EOT;

		$i = 0;
		foreach($where AS $row) {
			$sql .= PHP_EOL . ($i == 0 ? 'WHERE ' : 'AND ') . $row;
			$i++;
		}

		$sql .= PHP_EOL . <<<EOT
		ORDER BY
			CASE
				WHEN fylkesnummer = 50 THEN 0
				ELSE 1
			END,
			adressenavn,
			nummer,
			bokstav,
			poststed
		LIMIT 20;
		EOT;

		// Execute the query
		$request = $this->dbAdapter->query($sql);
		$result = $request->execute($parameters);
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


	public function getLastDbUpdate() : ?DateTime {
		$table = $this->tableName;
		$sql = "
			SELECT timestamp_created
			FROM $table
			ORDER BY timestamp_created ASC
			LIMIT 1;
		";
		$result = $this->dbAdapter->query($sql)->execute();
		if(!$result->count()) return null;
		return new DateTime($result->current()['timestamp_created']);
	}

}
