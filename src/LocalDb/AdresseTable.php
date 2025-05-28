<?php
/**
 * User: ingvar.aasen
 * Date: 2025-05-28
 */

namespace Iaasen\Matrikkel\LocalDb;

class AdresseTable extends AbstractTable
{
    protected string $tableName = 'matrikkel_adresser';


    public function insertRow(array $row) : void {
        $this->adresseRows[] = [
            'adresseId' => (int) $row[32],
            'fylkesnummer' => floor((int) $row[1] / 100),
            'kommunenummer' => (int) $row[1],
            'kommunenavn' => $row[2],
            'adressetype' => $row[3],
            'adressekode' => $row[6],
            'adressenavn' => $row[7],
            'nummer' => (int) $row[8],
            'bokstav' => $row[9],
            'gardsnummer' => (int) $row[10],
            'bruksnummer' => (int) $row[11],
            'festenummer' => (int) $row[12],
            'seksjonsnummer' => null,
            'undernummer' => (int) $row[13],
            'adresseTekst' => $row[14],
            'epsg' => (int) $row[16],
            'nord' => (float) $row[17],
            'øst' => (float) $row[18],
            'postnummer' => (int) $row[19],
            'poststed' => $row[20],
            'grunnkretsnavn' => $row[22],
            'soknenavn' => $row[24],
            'tettstednavn' => $row[27],
            'search_context' => $row[7] . ' ' . $row[8] . $row[9] . ' ' . $row[20] . ' ' . $row[27] . ' ' . $row[2],
        ];

        $this->cachedRows++;
        if($this->cachedRows >= 100) $this->flush();
    }

    public function insertRowLeilighetsnivaa(array $row) : void
    {
        $this->adresseRows[] = [
            'adresseId' => (int) $row[34],
            'fylkesnummer' => floor((int) $row[0] / 100),
            'kommunenummer' => (int) $row[0],
            'kommunenavn' => $row[1],
            'adressetype' => $row[2],
            'adressekode' => $row[5],
            'adressenavn' => $row[6],
            'nummer' => (int) $row[7],
            'bokstav' => $row[8],
            'gardsnummer' => (int) $row[9],
            'bruksnummer' => (int) $row[10],
            'festenummer' => (int) $row[11],
            'seksjonsnummer' => (int) $row[12],
            'undernummer' => (int) $row[13],
            'adresseTekst' => $row[17],
            'epsg' => (int) $row[18],
            'nord' => (float) $row[19],
            'øst' => (float) $row[20],
            'postnummer' => (int) $row[21],
            'poststed' => $row[22],
            'grunnkretsnavn' => $row[24],
            'soknenavn' => $row[26],
            'tettstednavn' => $row[29],
            'search_context' => $row[6] . ' ' . $row[7] . $row[8] . ' ' . $row[22] . ' ' . $row[29] . ' ' . $row[1],
        ];

        $this->cachedRows++;
        if($this->cachedRows >= 100) $this->flush();
    }

}
