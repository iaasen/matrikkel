<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\Matrikkel\Service;

use Iaasen\Matrikkel\Client\AdresseClient;
use Iaasen\Matrikkel\Client\BubbleId;
use Iaasen\Matrikkel\Client\MatrikkelsokClient;
use Iaasen\Matrikkel\Client\StoreClient;
use Iaasen\Matrikkel\Entity\Adresse;
use Iaasen\Matrikkel\Entity\Matrikkelenhet;
use Iaasen\Matrikkel\Entity\Veg;
use Iaasen\Service\ObjectKeyMatrix;

class AdresseService extends AbstractService {
	public function __construct(
		protected AdresseClient $adresseClient,
		protected StoreClient $storeClient,
		protected MatrikkelsokClient $matrikkelsokClient,
		protected KommuneService $kommuneService,
	) {}


	public function getAddressByAddressId(int $addressId) : ?Adresse {
		$result = $this->storeClient->getObject(['id' => BubbleId::getId($addressId, 'AdresseId')]);
		$adresse = new Adresse($result->return);
		return current($this->populate([$adresse]));
	}


	/**
	 * @param int[] $addressIds
	 * @return Adresse[]
	 */
	public function getAddressesByAddressIds(array $addressIds) : array {
		if(!count($addressIds)) return [];

		$idObjects = BubbleId::getIds($addressIds, 'AdresseId');
		$result = $this->storeClient->getObjects(['ids' => $idObjects]);
		$addresses =[];
		foreach($result->return->item AS $item) {
			$addresses[] = new Adresse($item);
		}
		return $this->populate($addresses);
	}


	/**
	 * @param Adresse[] $addresses
	 * @return Adresse[]
	 */
	protected function populate(array $addresses) : array {
		$addresses = $this->populateVeg($addresses);
		return $this->populateMatrikkelenhet($addresses);
	}


	/**
	 * @param Adresse[] $addresses
	 * @return Adresse[]
	 */
	protected function populateVeg(array $addresses) : array {
		$vegIdIndex = ObjectKeyMatrix::getObjectKeyMatrix($addresses, 'vegId');
		$result = $this->storeClient->getObjects(['ids' => BubbleId::getIds(array_keys($vegIdIndex), 'VegId')]);
		if(is_object($result->return->item)) $result->return->item = [$result->return->item];
		$vegObjects = [];
		foreach($result->return->item AS $item) {
			$vegObjects[] = new Veg($item);
		}
		ObjectKeyMatrix::populateObjectKeyMatrixWithAttribute($vegIdIndex, 'veg', $vegObjects, 'id');
		return $addresses;
	}


	/**
	 * @param Adresse[] $addresses
	 * @return Adresse[]
	 */
	protected function populateMatrikkelenhet(array $addresses) : array {
		$matrikkelIdIndex = ObjectKeyMatrix::getObjectKeyMatrix($addresses, 'matrikkelenhetId');
		$result = $this->storeClient->getObjects(['ids' => BubbleId::getIds(array_keys($matrikkelIdIndex), 'MatrikkelenhetId')]);
		if(is_object($result->return->item)) $result->return->item = [$result->return->item];
		$matrikkelObjects = [];
		foreach($result->return->item AS $item) {
			$matrikkelObjects[] = new Matrikkelenhet($item);
		}
		$matrikkelObjects = $this->populateKommune($matrikkelObjects);
		ObjectKeyMatrix::populateObjectKeyMatrixWithAttribute($matrikkelIdIndex, 'matrikkelenhet', $matrikkelObjects, 'id');
		return $addresses;
	}


	/**
	 * @param Matrikkelenhet[] $matrikkelenheter
	 * @return Matrikkelenhet[]
	 */
	protected function populateKommune(array $matrikkelenheter) : array {
		$matrikkelNumberObjects = [];
		foreach($matrikkelenheter AS $enhet) $matrikkelNumberObjects[] = $enhet->matrikkelnummer;
		$kommuneIdIndex = ObjectKeyMatrix::getObjectKeyMatrix($matrikkelNumberObjects, 'kommuneId');
		$kommuner = $this->kommuneService->getKommunerByIds(array_keys($kommuneIdIndex));
		ObjectKeyMatrix::populateObjectKeyMatrixWithAttribute($kommuneIdIndex, 'kommune', $kommuner, 'id');
		return $matrikkelenheter;
	}


	/**
	 * @param string $search Fuzzy search
	 * @return Adresse[] Addresses
	 */
	public function searchAddress(string $search) : array {
		$result = $this->matrikkelsokClient->findTekstelementerForAutoutfylling(
			[
				'sokeStreng' => $search,
				'parametre' => 'KILDE:Adresse',
				'returFelter' => ['ID', 'OBJEKTTYPE'],
				'startPosisjon' => 0,
			]
		);

		// Empty result
		if(!isset($result->return->item)) return [];

		$addressIds = [];
		foreach($result->return->item AS $item) {
			$match = [];
			preg_match('/ID: (.+), OBJEKTTYPE: (.+)/', $item, $match);
			if($match[2] == 'VEGADRESSE') $addressIds[$match[1]] = $match[2];
		}
		return $this->getAddressesByAddressIds(array_keys($addressIds));
	}

}
