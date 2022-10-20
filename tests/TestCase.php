<?php

namespace BiiiiiigMonster\Remote\Tests;

use BiiiiiigMonster\Remote\RemoteServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            RemoteServiceProvider::class,
        ];
    }
}
