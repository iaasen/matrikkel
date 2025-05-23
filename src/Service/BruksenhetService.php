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

        $bruksenhetIds = [];

        // Empty result
        if(!isset($result->return->item)) return [];

        // Single result
        if(is_object($result->return->item)) {
            $bruksenhetIds[] = $result->return->item->value;
        }

        // Multiple results
        if(is_array($result->return->item)) {
            foreach($result->return->item AS $row) {
                $bruksenhetIds[] = $row->value;
            }
        }

        // Collect the data
        $bruksenheter = [];
        foreach($bruksenhetIds AS $id) {
            $bruksenheter[] = $this->getBruksenhetById($id);
        }

        return $bruksenheter;
	}

    /**
     * @param array $addressIds
     * @return array
     */
    public function getBruksenheterByAdresseIds(array $addressIds): array
    {
        $bubbleIds = [];
        foreach($addressIds AS $addressId) {
            $bubbleIds[] = BubbleId::getId($addressId, 'AdresseId');
        }
        $result = $this->bruksenhetClient->findBruksenheterForAdresser(['adresseIds' => $bubbleIds]);

        // Empty result
        if(!isset($result->return->entry)) return [];

        // Make a simple result array
        $response = [];
        $rowEntries = is_object($result->return->entry) ? [$result->return->entry] : $result->return->entry;

        foreach($rowEntries as $addressRow) {
            $entry = [
                'adresseId' => $addressRow->key->value,
                'bruksenheter' => [],
            ];
            $bruksenhetEntries = is_object($addressRow->value->item) ? [$addressRow->value->item] : $addressRow->value->item;
            foreach($bruksenhetEntries AS $bruksenhetRow) {
                $entry['bruksenheter'][] = $this->getBruksenhetById(is_int($bruksenhetRow) ? $bruksenhetRow : $bruksenhetRow->value);
            }
            $response[] = $entry;
        }

        return $response;
    }

}
