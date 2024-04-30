<?php
/**
 * User: ingvar.aasen
 * Date: 29.04.2024
 */

namespace Iaasen\Matrikkel\Service;

use Iaasen\Matrikkel\Client\BruksenhetClient;
use Iaasen\Matrikkel\Client\BubbleId;
use Iaasen\Matrikkel\Client\StoreClient;
use Iaasen\Matrikkel\Entity\Bruksenhet;

/**
 * @see https://prodtest.matrikkel.no/matrikkelapi/wsapi/v1/dokumentasjon/BruksenhetService.html
 */
class BruksenhetService extends AbstractService {

	public function __construct(
		protected BruksenhetClient $bruksenhetClient,
		protected StoreClient $storeClient,
	) {}


	public function getBruksenhetById(int $id): Bruksenhet {
		return new Bruksenhet($this->storeClient->getObject(['id' => BubbleId::getId($id, 'BruksenhetId')])->return);
	}


	/**
	 * @param int $addressId
	 * @return Bruksenhet[]
	 */
	public function getBruksenheterByAdresseId(int $addressId) : array {
		$result = $this->bruksenhetClient->findBruksenheterForAdresse(['adresseId' => BubbleId::getId($addressId, 'AdresseId')]);
		$bruksenheter = [];
		foreach($result->return->item AS $item) {
			$bruksenheter[] = $this->getBruksenhetById($item->value);
		}
		return $bruksenheter;
	}

}
