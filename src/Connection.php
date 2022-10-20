<?php


namespace BiiiiiigMonster\Remote;

use Illuminate\Database\Connection as BaseConnection;
use Illuminate\Database\Query\Builder;

class Connection extends BaseConnection
{
    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return new Builder($this, new Grammar());
    }

    public function insert($query, $bindings = [])
    {
        var_dump($query);
        var_dump($bindings);
        die;
    }
}