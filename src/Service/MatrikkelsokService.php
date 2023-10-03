<?php
/**
 * User: ingvar.aasen
 * Date: 03.10.2023
 */

namespace Iaasen\Matrikkel\Service;

use Iaasen\Matrikkel\Client\MatrikkelsokClient;
use Iaasen\Matrikkel\Entity\Matrikkelsok\AbstractMatrikkelsok;
use Iaasen\Matrikkel\Entity\Matrikkelsok\Eiendom;
use Iaasen\Matrikkel\Entity\Matrikkelsok\Veg;
use Iaasen\Matrikkel\Entity\Matrikkelsok\Vegadresse;

class MatrikkelsokService {

	public function __construct(
		protected MatrikkelsokClient $matrikkelsokClient,
	) {}


	/**
	 * @param string $search
	 * @return AbstractMatrikkelsok[]
	 */
	public function searchAddresses(string $search) : array {
		$result = $this->matrikkelsokClient->findTekstelementerForAutoutfylling([
			'sokeStreng' => $search,
			'parametre' => 'OBJEKTTYPE:Vegadresse',
			'returFelter' => [],
			'startPosisjon' => 0,
		]);

		if(!isset($result->return->item)) $items = [];
		elseif(is_string($result->return->item)) $items = [$result->return->item];
		else $items = $result->return->item;

		$addresses = [];
		foreach($items AS $item) {
			if($address = self::convertSearchResultToObject($item)) $addresses[] = $address;
		}
		return $addresses;
	}


	protected static function convertSearchResultToObject(string $row) : ?AbstractMatrikkelsok {
		$pattern = '/^ ?(' . implode('|', AbstractMatrikkelsok::SEARCH_RESULT_FIELD_NAMES) . '): (.*)/';

		$segments = explode(',', $row);
		$fields = [];
		$lastField = null;
		foreach($segments AS $segment) {
			$matches = [];
			// New field
			if(preg_match($pattern, $segment, $matches)) {
				$fields[$matches[1]] = $matches[2];
				$lastField = $matches[1];
			}
			// Append to last field
			else {
				if(strlen($fields[$lastField])) $fields[$lastField] .= ', ';
				$fields[$lastField] .= trim($segment);
			}
		}

		switch ($fields['OBJEKTTYPE']) {
			case 'VEGADRESSE': return new Vegadresse($fields);
			case 'VEG': return new Veg($fields);
			case 'EIENDOM': return new Eiendom($fields);
			default: return null;
		}
	}

}
