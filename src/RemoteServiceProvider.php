<?php

namespace BiiiiiigMonster\Remote;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class RemoteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Model::setConnectionResolver(tap(
            $this->app->get('db'),
            fn (DatabaseManager $db) => $db->extend('remote', function($config, $name){
                $config['name'] = $name;
                return new Connection($config);
            })
        ));
    }
}