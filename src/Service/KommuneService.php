<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\Matrikkel\Service;

use Iaasen\Matrikkel\Client\BubbleId;
use Iaasen\Matrikkel\Client\KommuneClient;
use Iaasen\Matrikkel\Client\StoreClient;
use Iaasen\Matrikkel\Entity\Kommune;

class KommuneService extends AbstractService {

	public function __construct(
		protected KommuneClient $kommuneClient,
		protected StoreClient $storeClient
	) {}


	public function getKommuneById(int $id) : ?Kommune {
		$result = $this->storeClient->getObject([
			'id' => BubbleId::getId($id, 'KommuneId'),
		]);
		return new Kommune($result->return);
	}


	public function getKommuneByNumber(string|int $number) : ?Kommune {
		return $this->getKommuneById((int) $number);
	}


	/**
	 * @param int[] $ids
	 * @return Kommune[]
	 */
	public function getKommunerByIds(array $ids) : array {
		$result = $this->storeClient->getObjects(['ids' => BubbleId::getIds($ids, 'KommuneId')]);
		if(is_object($result->return->item)) $result->return->item = [$result->return->item];
		$kommuner = [];
		foreach($result->return->item as $item) {
			$kommuner[] = new Kommune($item);
		}
		return $kommuner;
	}

}
