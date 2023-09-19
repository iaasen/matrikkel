<?php
/**
 * User: ingvar.aasen
 * Date: 19.09.2023
 */

namespace Iaasen\MatrikkelApi\Entity;

use Iaasen\Model\AbstractEntityV2;

class KodeNavn extends AbstractEntityV2 {
	protected string $key; // Language
	protected string $value; // Value in given language
}
