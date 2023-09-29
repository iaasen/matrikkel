<?php
/**
 * User: ingvar.aasen
 * Date: 29.09.2023
 */

namespace Iaasen\Matrikkel\Service;

use Iaasen\Matrikkel\Client\BubbleId;
use Iaasen\Matrikkel\Client\MatrikkelenhetClient;
use Iaasen\Matrikkel\Client\StoreClient;
use Iaasen\Matrikkel\Entity\Matrikkelenhet;

class MatrikkelenhetService extends AbstractService {

	public function __construct(
		protected MatrikkelenhetClient $matrikkelenhetClient,
		protected StoreClient          $storeClient,
	) {}


//	public function findMatrikkelenheterForAdresse(int $addressId): array {
//		$result = $this->matrikkelenhetClient->findMatrikkelenheterForAdresse([
//			'adresseId' => BubbleId::getId($addressId, 'AdresseId'),
//			'taMedUtgatte' => false,
//			'viaBruksenhet' => false,
//		]);
//		dump($result);
//		$result = $this->storeClient->getObject(['id' => BubbleId::getId($result->return->item->value, 'MatrikkelenhetId')]);
//		dd($result);
//		$adresse = new Matrikkelenhet($result->return);
//		return current($this->populate([$adresse]));
//	}


	public function getMatrikkelenhetById(int $id) {
		$result = $this->storeClient->getObject(['id' => BubbleId::getId($id, 'MatrikkelenhetId')]);
		return new Matrikkelenhet($result->return);
	}


	public function getMatrikkelenhetByMatrikkel(int $knr, int $gnr, int $bnr, int $fnr = 0, int $snr = 0) : Matrikkelenhet {
		$result = $this->matrikkelenhetClient->findMatrikkelenhet([
			'matrikkelenhetIdent' => [
				'kommuneIdent' => [
					'kommunenummer' => str_repeat('0', 4-strlen((string)$knr)) . $knr,
				],
				'gardsnummer' => $gnr,
				'bruksnummer' => $bnr,
				'festenummer' => $fnr,
				'seksjonsnummer' => $snr,
			],
		]);
		$result = $this->storeClient->getObject(['id' => BubbleId::getId($result->return->value, 'MatrikkelenhetId')]);
		return new Matrikkelenhet($result->return);
	}

}
