<?php


namespace BiiiiiigMonster\Remote;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Connection as BaseConnection;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor;
use Illuminate\Support\Str;
use PHPSQLParser\PHPSQLParser;
use Closure;
use RuntimeException;

class Connection extends BaseConnection
{
    protected $client;

    public function __construct(array $config = [])
    {
        parent::__construct(null, '', $config['prefix'], $config);

        $this->client = new Client();
    }

    protected function getDefaultQueryGrammar()
    {
        return new MySqlGrammar;
    }

    protected function getDefaultPostProcessor()
    {
        return new MySqlProcessor;
    }

    public function select($query, $bindings = [], $useReadPdo = true)
    {
        return $this->run($query, $bindings, function ($parsed) {
            if ($this->pretending()) {
                return [];
            }

            $method = 'GET';
            $url = '';
            $body = [];

            $request = new Request($method,$url,[],$body);
            $response = $this->client->send($request);

            return json_decode((string)$response->getBody(), true);
        });
    }

    public function cursor($query, $bindings = [], $useReadPdo = true)
    {
        $statement = $this->run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            // First we will create a statement for the query. Then, we will set the fetch
            // mode and prepare the bindings for the query. Once that's done we will be
            // ready to execute the query against the database and return the cursor.
            $statement = $this->prepared($this->getPdoForSelect($useReadPdo)
                ->prepare($query));

            $this->bindValues(
                $statement, $this->prepareBindings($bindings)
            );

            // Next, we'll execute the query against the database and return the statement
            // so we can return the cursor. The cursor will use a PHP generator to give
            // back one row at a time without using a bunch of memory to render them.
            $statement->execute();

            return $statement;
        });

        while ($record = $statement->fetch()) {
            yield $record;
        }
    }

    public function statement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($parsed) {
            if ($this->pretending()) {
                return true;
            }

            $method = 'GET';
            $url = '';
            $body = [];

            $request = new Request($method,$url,[],$body);
            $response = $this->client->send($request);

            return $response->getStatusCode() === 200;
        });
    }

    public function affectingStatement($query, $bindings = [])
    {
        return $this->run($query, $bindings, function ($parsed) {
            if ($this->pretending()) {
                return 0;
            }

            $method = 'GET';
            $url = '';
            $body = [];

            $request = new Request($method,$url,[],$body);
            $response = $this->client->send($request);

            $this->recordsHaveBeenModified(
                ($count = (int)(string)$response->getBody()) > 0
            );

            return $count;
        });
    }

    public function unprepared($query)
    {
        throw new RuntimeException('This connection driver dose not support unprepared.');
    }

    protected function runQueryCallback($query, $bindings, Closure $callback)
    {
        $parser = new PHPSQLParser(Str::replaceArray('?', $bindings, $query));

        return $callback($parser->parsed);
    }
}