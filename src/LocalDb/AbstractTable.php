<?php
/**
 * User: ingvar.aasen
 * Date: 2025-05-28
 */

namespace Iaasen\Matrikkel\LocalDb;

use Iaasen\DateTime;
use Laminas\Db\Adapter\Adapter;

class AbstractTable
{
    protected string $tableName;
    protected array $adresseRows = [];
    protected int $cachedRows = 0;

    public function __construct(
        protected Adapter $dbAdapter
    ) {}

    public function flush() : void
    {
        if(!count($this->adresseRows)) return;

        $sql = $this->getStartInsert();
        $valueRows = [];
        foreach($this->adresseRows as $adresseRow) {
            foreach($adresseRow AS $key => $column) {
                $adresseRow[$key] = '"' . $column . '"';
            }
            $valueRows[] .= '(' . implode(',', $adresseRow) . ')';
        }
        $sql .= implode(",\n", $valueRows);
        $sql .= ';';
        $this->dbAdapter->query($sql)->execute();
        $this->adresseRows = [];
        $this->cachedRows = 0;
    }

    public function getStartInsert() : string
    {
        $columnNames = array_keys(current($this->adresseRows));
        $columnsString = array_map(function ($column) { return '`' . $column . '`'; }, $columnNames);
        $columnsString = implode(',', $columnsString);
        $columnsString = '(' . $columnsString . ')';
        return 'REPLACE INTO ' . $this->tableName . ' ' . $columnsString . PHP_EOL . 'VALUES' . PHP_EOL;
    }

    public function deleteOldRows() : int
    {
        $date = new DateTime();
        $date->modify('-3 hour'); // Go back 3 hours to get before UTC in case of timezone errors
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE timestamp_created < "' . $date->formatMysql() . '";';
        $result = $this->dbAdapter->query($sql)->execute();
        return $result->getAffectedRows();
    }

    public function countDbAddressRows() : int {
        $sql = 'SELECT COUNT(*) FROM ' . $this->tableName . ';';
        $result = $this->dbAdapter->query($sql)->execute();
        return current($result->current());
    }

    public function truncateTable(): void
    {
        if(!str_starts_with($this->tableName, 'matrikkel')) return;
        $sql = 'TRUNCATE TABLE ' . $this->tableName . ';';
        $this->dbAdapter->query($sql)->execute();
    }

}
