<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Passport\Client;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('passport:keys --force --quiet');
        $this->createPassportClient();
    }

    private function createPassportClient(): void
    {
        Client::create([
            'name'          => 'Test Personal Access Client',
            'secret'        => \Illuminate\Support\Str::random(40),
            'provider'      => 'users',
            'redirect_uris' => [],
            'grant_types'   => ['personal_access'],
            'revoked'       => false,
        ]);
    }
}
