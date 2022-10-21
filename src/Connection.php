<?php


namespace BiiiiiigMonster\Remote;

use Illuminate\Database\Connection as BaseConnection;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use PHPSQLParser\PHPSQLParser;

class Connection extends BaseConnection
{
    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return new Builder($this, new MySqlGrammar());
    }

    public function insert($query, $bindings = [])
    {
        $parser = new PHPSQLParser($query);
        var_dump($query);
        var_dump($bindings);

        die;
    }
}