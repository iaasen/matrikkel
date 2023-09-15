<?php
/**
 * User: ingvar.aasen
 * Date: 15.09.2023
 */

namespace Iaasen\MatrikkelApi\Service;

use Iaasen\MatrikkelApi\Client\AdresseClient;

class AdresseService extends AbstractService {
	public function __construct(
		protected AdresseClient $adresseClient
	) {}


	public function searchAddress(string $search) : array {
		return [];
	}
}