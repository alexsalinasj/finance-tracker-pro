<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthRoutesTest extends TestCase
{
    public function test_dashboard_requires_authentication(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }
}
