<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\Matrikkel\Service;

use Iaasen\Exception\InvalidArgumentException;
use Iaasen\Geonorge\Entity\LocationLatLong;
use Iaasen\Geonorge\Entity\LocationUtm;
use Iaasen\Geonorge\TranscodeService;
use Iaasen\Matrikkel\Client\AdresseClient;
use Iaasen\Matrikkel\Client\BubbleId;
use Iaasen\Matrikkel\Client\MatrikkelenhetClient;
use Iaasen\Matrikkel\Client\MatrikkelsokClient;
use Iaasen\Matrikkel\Client\StoreClient;
use Iaasen\Matrikkel\Entity\Adresse;
use Iaasen\Matrikkel\Entity\Bruksenhet;
use Iaasen\Matrikkel\Entity\Krets;
use Iaasen\Matrikkel\Entity\Matrikkelenhet;
use Iaasen\Matrikkel\Entity\Representasjonspunkt;
use Iaasen\Matrikkel\Entity\Veg;
use Iaasen\Service\ObjectKeyMatrix;

class AdresseService extends AbstractService {
	protected TranscodeService $transcodeService;

	public function __construct(
		protected AdresseClient $adresseClient,
		protected StoreClient $storeClient,
		protected MatrikkelsokClient $matrikkelsokClient,
		protected MatrikkelenhetClient $matrikkelenhetClient,
		protected KommuneService $kommuneService
	) {
		$this->transcodeService = new TranscodeService();
	}


	public function getAddressById(int $addressId) : ?Adresse {
		return $this->createAddress($addressId);
	}


	/**
	 * @param int[] $addressIds
	 * @return Adresse[]
	 */
	public function getAddressesByAddressIds(array $addressIds) : array {
		if(!count($addressIds)) return [];
		$addresses = [];
		foreach($addressIds AS $addressId) {
			$address = $this->createAddress($addressId);
			if($address) $addresses[] = $address;
		}
		return $addresses;
	}


	/**
	 * @deprecated Use createAddress()
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
	 * @deprecated Use createAddress()
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
		$matrikkelObjects = $this->populateKommune($matrikkelObjects);
		ObjectKeyMatrix::populateObjectKeyMatrixWithAttribute($matrikkelIdIndex, 'matrikkelenhet', $matrikkelObjects, 'id');
		return $addresses;
	}


	/**
	 * @deprecated Use createAddress()
	 * @param Adresse[] $addresses
	 * @return Adresse[]
	 */
	protected function populatePostnummeromrade(array $addresses) : array {
		foreach($addresses AS $address) {
			$address->postnummeromrade = null;
			$result = $this->adresseClient->findObjekterForAdresse(['adresseId' => BubbleId::getId($address->id, 'AdresseId')]);
			foreach($result->return->bubbleObjects->item AS $item) {
				if(isset($item->kretstypeKodeId) && $item->kretstypeKodeId->value == 4097)
					$address->postnummeromrade = new Krets\Postnummeromrade($item);
			}
		}
		return $addresses;
	}


	protected function createAddress(int $addressId) : ?Adresse {
		$address = null;

		try {
			$result = $this->adresseClient->findObjekterForAdresse(['adresseId' => BubbleId::getId($addressId, 'AdresseId')]);
		}
		catch (\Exception $e) {
			if(str_contains($e->getMessage(), 'AdresseId')) {
				return null;
			}
			else throw $e;
		}
		//dump($this->adresseClient->getLastResponse());
		// TODO: Return null if the address id is wrong

		$bubbles = $result->return->bubbleObjects->item;
		if (is_object($bubbles)) $bubbles = [$bubbles];

		// Create the Address itself
		foreach ($bubbles as $key => $bubble) {
			if ($addressId == $bubble->id->value) {
				$address = new Adresse($bubble);
				unset($bubbles[$key]);
			}
		}
		if (!$address) return null;

		// Populate
		foreach ($bubbles as $bubble) {
			if (isset($bubble->kretstypeKodeId)) {
				switch($bubble->kretstypeKodeId->value) {
					case '4097':
						$address->postnummeromrade = new Krets\Postnummeromrade($bubble);
						break;
					case '4098':
						$address->tettsted = new Krets\Tettsted($bubble);
						break;
					case '4099':
						$address->kirkesokn = new Krets\Kirkesokn($bubble);
						break;
					case '4100':
						$address->grunnkrets = new Krets\Grunnkrets($bubble);
						break;
					case '4101':
						$address->stemmekrets = new Krets\Stemmekrets($bubble);
						break;
					case '4102':
						$address->svalbardomrade = new Krets\Svalbardomrade($bubble);
						break;
					default:
						// Kommunalkrets?
						break;
				}
			}
			elseif(isset($bubble->etasjeplanKodeId)) $address->addBruksenhet(new Bruksenhet($bubble));
			elseif(isset($bubble->matrikkelnummer)) $address->matrikkelenhet = new Matrikkelenhet($bubble);
			elseif($bubble->id->value == $address->vegId) $address->veg = new Veg($bubble);
			//elseif(isset($bubble->bygningsnummer)) {} // Must add bygning
			//else throw new InvalidArgumentException('Unknown bubble object type');
		}
		return $address;
	}


	/**
	 * @param Matrikkelenhet[] $matrikkelenheter
	 * @return Matrikkelenhet[]
	 */
	protected function populateKommune(array $matrikkelenheter) : array {
		$matrikkelNumberObjects = [];
		foreach($matrikkelenheter AS $enhet) $matrikkelNumberObjects[] = $enhet->matrikkelnummer;
		$kommuneIdIndex = ObjectKeyMatrix::getObjectKeyMatrix($matrikkelNumberObjects, 'kommuneId');
		$kommuner = $this->kommuneService->getKommunerByIds(array_keys($kommuneIdIndex));
		ObjectKeyMatrix::populateObjectKeyMatrixWithAttribute($kommuneIdIndex, 'kommune', $kommuner, 'id');
		return $matrikkelenheter;
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

		// Empty result
		if(!isset($result->return->item)) return [];

		$addressIds = [];
		if(is_string($result->return->item)) $result->return->item = [$result->return->item];
		foreach($result->return->item AS $item) {
			$match = [];
			preg_match('/ID: (.+), OBJEKTTYPE: (.+)/', $item, $match);
			if($match[2] == 'VEGADRESSE') $addressIds[$match[1]] = $match[2];
		}
		return $this->getAddressesByAddressIds(array_keys($addressIds));
	}


	public function getPostnummeromradeById(int $id) : ?Krets\Postnummeromrade {
		try {
			$result = $this->storeClient->getObject([
				'id' => BubbleId::getId($id, 'PostnummeromradeId')
			]);
		}
		catch (\SoapFault $e) {
			if(str_contains($e->getMessage(), '[PostnummeromradeId')) return null;
			throw $e;
		}
		return new Krets\Postnummeromrade($result->return);
	}


	public function getPostnummeromradeByNumber(int $postnr) : ?Krets\Postnummeromrade {
		try {
			$result = $this->adresseClient->findPostnummeromrade([
				'postnr' => $postnr,
			]);
		}
		catch (\SoapFault $e) {
			if(str_contains($e->getMessage(), 'Kunne ikke finne')) return null;
			throw $e;
		}
		$id = $result->return->value;
		return $this->getPostnummeromradeById($id);
	}


	public function getLocationUtm(Representasjonspunkt $rep, int $zone = 32) : LocationUtm {
		if($rep->isUtm()) {
			if($rep->getUtmZone() == $zone) return new LocationUtm($rep->y, $rep->x, $rep->getUtmZone() . 'N');
			else return $this->transcodeService->transcodeUtmZoneToUtmZone($rep->y, $rep->x, $rep->getUtmZone(), $zone);
		}
		elseif($rep->isLatLong()) {
			return $this->transcodeService->transcodeLatLongToUTM($rep->y, $rep->x, $zone);
		}
		throw new InvalidArgumentException('Unknown coordinate system');
	}


	public function getLocationLatLong(Representasjonspunkt $rep) : LocationLatLong {
		if($rep->isUtm()) {
			return $this->transcodeService->transcodeUTMtoLatLong($rep->y, $rep->x, $rep->getUtmZone());
		}
		elseif($rep->isLatLong()) {
			return new LocationLatLong($rep->y, $rep->x);
		}
		throw new InvalidArgumentException('Unknown coordinate system');
	}

}
