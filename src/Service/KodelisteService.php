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
			$codeIds = [];
			foreach($result->return->koderIds->item AS $id) {
				$codeIds[] = $id->value;
			}
			$idObjects = BubbleId::getIds($codeIds, $kodeliste->kodeIdType, $kodeliste->kodeIdNamespace);
			$kodeliste->koderIds = [];
			$result = $this->storeClient->getObjects(['ids' => $idObjects]);
			foreach($result->return->item AS $row) {
				$kodeliste->addKode(new Kode($row));
			}
		}

		return $kodeliste;
	}


	public function getKode(int $id, string $kodeIdType, string $kodeIdNamespace) : ?Kode {
		$result = $this->storeClient->getObject(['id' => BubbleId::getId($id, $kodeIdType, $kodeIdNamespace)]);
		return new Kode($result->return);
	}

}
