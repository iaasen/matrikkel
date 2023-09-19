<?php
/**
 * User: ingvar.aasen
 * Date: 19.09.2023
 */

namespace Iaasen\MatrikkelApi\Entity;

use Iaasen\MatrikkelApi\Client\BubbleId;
use Iaasen\Model\AbstractEntityV2;

/**
 * @property int $id
 * @property string $kodeIdClass
 * @property string $kodeIdNamespace
 * @property string $kodeIdType
 * @property Kode[] $koderIds;
 */
class Kodeliste extends AbstractEntityV2 {
	protected int $id;
	protected string $kodeIdClass;
	protected string $kodeIdNamespace;
	protected string $kodeIdType;
	/** @var \Iaasen\MatrikkelApi\Entity\Kode[]  */
	protected array $koderIds;


	public function setId(object|int $id) : void {
		if(is_object($id)) {
			$this->id = $id->value;
		}
		else $this->id = $id;
	}


	public function setKodeIdClass(string $class) : void {
		$this->kodeIdClass = $class;
		$this->generateKodeIdNamespace();
		$this->generateKodeIdType();
	}


	public function setKoderIds(object|array $koderIds) : void {
		if(is_array($koderIds)) $this->koderIds = $koderIds;
	}


	public function addKode(Kode $kode) {
		$this->koderIds[] = $kode;
	}


	protected function generateKodeIdNamespace() : void {
		$matches = [];
		preg_match(
			'/^no\.statkart\.matrikkel\.matrikkelapi\.wsapi\.v1\.domain\.(.+)\..+$/',
			$this->kodeIdClass,
			$matches
		);
		$this->kodeIdNamespace = BubbleId::NAMESPACE_BASE . str_replace('.', '/', $matches[1]);
	}


	protected function generateKodeIdType() : void {
		$matches = [];
		preg_match(
			'/^no\.statkart\.matrikkel\.matrikkelapi\.wsapi\.v1\.domain\..+\.(.+)$/',
			$this->kodeIdClass,
			$matches
		);
		$this->kodeIdType = $matches[1];
	}

}
