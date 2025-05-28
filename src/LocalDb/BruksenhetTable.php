<?php
/**
 * User: ingvar.aasen
 * Date: 2025-05-28
 */

namespace Iaasen\Matrikkel\LocalDb;

class BruksenhetTable extends AbstractTable
{
    protected string $tableName = 'matrikkel_bruksenheter';

    public function insertRow(array $row) : void {
        $this->adresseRows[] = [
            'adresseId' => (int) $row[34],
            'bruksenhet' => $row[15] ?: 'H0101',
        ];

        $this->cachedRows++;
        if($this->cachedRows >= 100) $this->flush();
    }

}
