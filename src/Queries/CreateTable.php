<?php

namespace Highideas\SqlToMigration\Queries;

use Highideas\SqlToMigration\Exceptions\InvalidQueryException;
use Highideas\SqlToMigration\Queries\Statements\StatementFactory;

class CreateTable implements QueryInterface
{
    protected $query;
    protected $table;
    protected $statements;
    protected $rawStatements;

    public function __construct($query)
    {
        $this->query = $query;
        $this->run();
    }

    protected function run()
    {
        $this->defineTable();
        $this->defineStatements();
    }

    protected function defineTable()
    {
        $outputArray = [];
        preg_match(
            "/(CREATE TABLE IF NOT EXISTS|CREATE TABLE)[\s|`|']+([0-9a-zA-Z-_]+)[\s|`|'|\(](.*)/i",
            $this->query,
            $outputArray
        );
        if (empty($outputArray)) {
            throw new InvalidQueryException($this->query, 'Invalid Table.');
        }
        $this->table = $outputArray[2];
        $this->rawStatements = $outputArray[3];
    }

    protected function defineStatements()
    {
        $this->statements = StatementFactory::instantiate($this->rawStatements);
    }

    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return \Highideas\SqlToMigration\Collections\Collection Collection Instance
     */
    public function getColumns()
    {
        return $this->statements->getCollection();
    }

    /**
     * @return \Highideas\SqlToMigration\Queries\Statements\Statement Statement Instance
     */
    public function getStatement()
    {
        return $this->statements;
    }

    public function getQuery()
    {
        return $this->query;
    }
}
