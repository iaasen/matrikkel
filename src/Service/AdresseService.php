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

class AdresseService extends AbstractService {
	public function __construct(
		protected AdresseClient $adresseClient,
		protected StoreClient $storeClient,
		protected MatrikkelsokClient $matrikkelsokClient,
	) {}


	public function getAddressByAddressId(int $addressId) : ?Adresse {
		$result = $this->storeClient->getObject(['id' => BubbleId::getId($addressId, 'AdresseId')]);
		$adresse = new Adresse($result->return);

		$result = $this->storeClient->getObject(['id' => BubbleId::getId($adresse->vegId, 'VegId')]);
		$adresse->veg = new Veg($result->return);

		$result = $this->storeClient->getObject(['id' => BubbleId::getId($adresse->matrikkelenhetId, 'MatrikkelenhetId')]);
		$adresse->matrikkelenhet = new Matrikkelenhet($result->return);

		return $adresse;
	}


	public function searchAddress(string $search) : array {
		return [];
	}
}