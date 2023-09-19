<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\MatrikkelApi\Service;

use Iaasen\MatrikkelApi\Client\BubbleId;
use Iaasen\MatrikkelApi\Client\KodelisteClient;
use Iaasen\MatrikkelApi\Client\StoreClient;
use Iaasen\MatrikkelApi\Entity\Kode;
use Iaasen\MatrikkelApi\Entity\Kodeliste;

class KodelisteService extends AbstractService {

	public function __construct(
		protected KodelisteClient $kodelisteClient,
		protected StoreClient $storeClient,
	) {}


	/**
	 * @return Kodeliste[]
	 */
	public function getKodelister() : array {
		$result = $this->kodelisteClient->getKodelisterEnkel();

		$kodelister = [];
		foreach($result->return->kodelisterIds->item AS $id) {
			$kodelister[] = $this->getKodeliste($id->value, false);
		}
		return $kodelister;
	}


	public function getKodeliste(int $kodelisteId, bool $withCodes = false) : Kodeliste {
		$result = $this->storeClient->getObject(['id' => BubbleId::getId($kodelisteId, 'KodelisteId')]);
		$kodeliste = new Kodeliste($result->return);
		if($withCodes) {
			$kodeliste->koderIds = [];
			foreach($result->return->koderIds->item AS $kodeId) {
				$kode = $this->getKode($kodeId->value, $kodeliste->kodeIdType, $kodeliste->kodeIdNamespace);
				$kodeliste->addKode($kode);
			}
		}
		return $kodeliste;
	}


	public function getKode(int $id, string $kodeIdType, string $kodeIdNamespace) : ?Kode {
		$result = $this->storeClient->getObject(['id' => BubbleId::getId($id, $kodeIdType, $kodeIdNamespace)]);
		return new Kode($result->return);
	}

}
