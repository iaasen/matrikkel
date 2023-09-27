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


	public function getKommuneById(int $id) : object {
		$result = $this->storeClient->getObject([
			'id' => BubbleId::getId($id, 'KommuneId'),
		]);
		return new Kommune($result->return);
	}


	public function getKommuneByNumber(int $number) : object {
		return $this->getKommuneById($number);
	}

}
