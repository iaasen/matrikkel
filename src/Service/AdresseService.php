<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\MatrikkelApi\Service;

use Iaasen\MatrikkelApi\Client\AdresseClient;
use Iaasen\MatrikkelApi\Client\BubbleId;
use Iaasen\MatrikkelApi\Client\MatrikkelsokClient;
use Iaasen\MatrikkelApi\Client\StoreClient;
use Iaasen\MatrikkelApi\Entity\Adresse;
use Iaasen\MatrikkelApi\Entity\Matrikkelenhet;
use Iaasen\MatrikkelApi\Entity\Veg;
use Iaasen\Service\ObjectKeyMatrix;

class AdresseService extends AbstractService {
	public function __construct(
		protected AdresseClient $adresseClient,
		protected StoreClient $storeClient,
		protected MatrikkelsokClient $matrikkelsokClient,
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
		ObjectKeyMatrix::populateObjectKeyMatrixWithAttribute($matrikkelIdIndex, 'matrikkelenhet', $matrikkelObjects, 'id');
		return $addresses;
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
		$addressIds = [];
		foreach($result->return->item AS $item) {
			$match = [];
			preg_match('/ID: (.+), OBJEKTTYPE: (.+)/', $item, $match);
			$addressIds[$match[1]] = $match[2];
		}
		return $this->getAddressesByAddressIds(array_keys($addressIds));
	}

}
