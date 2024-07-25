<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     *
     * A basic test example.
     */
    #[Test]
    public function hello_world_returns_a_successful_response(): void
    {
        #teniendo


        #haciendo
        $response = $this->get('api/v1/hello-world');

        #esperando
        $response->assertJsonStructure(['msg']);
        $response->assertJson(['msg' => 'Hello World!']);
        $response->assertStatus(200);

    }
}
