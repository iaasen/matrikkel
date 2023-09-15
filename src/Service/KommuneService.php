<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\MatrikkelApi\Service;

use Iaasen\MatrikkelApi\Client\BubbleId;
use Iaasen\MatrikkelApi\Client\KommuneClient;
use Iaasen\MatrikkelApi\Client\StoreClient;

class KommuneService extends AbstractService {

	public function __construct(
		protected KommuneClient $kommuneClient,
		protected StoreClient $storeClient
	) {}


	public function getKommuneById(int $id) : object {
		return $this->storeClient->getObject([
			'id' => BubbleId::getId('KommuneId', $id),
		]);
	}


	public function getKommuneByNumber(int $number) : object {
		return $this->getKommuneById($number);
	}

}
