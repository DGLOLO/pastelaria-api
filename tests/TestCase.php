<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Config;

abstract class TestCase extends BaseTestCase
{
    /**
     * Cria a aplicação para testes
     * As variáveis de ambiente do phpunit.xml são automaticamente carregadas
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        
        $app->make(Kernel::class)->bootstrap();
        
        // Força o uso do SQLite em memória para testes
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        
        return $app;
    }
}
