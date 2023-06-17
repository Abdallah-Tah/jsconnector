<?php


namespace Amohamed\JSConnector\Tests;

use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Http;
use Amohamed\JSConnector\Facades\JSConnector;
use Amohamed\JSConnector\JSConnectorServiceProvider;




class JSConnectorServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [JSConnectorServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'JSConnector' => JSConnector::class,
        ];
    }

    /** @test */
    public function it_makes_a_post_request()
    {
        Http::fake([
            '*' => Http::response(['foo' => 'bar'], 200),
        ]);

        $response = JSConnector::post('test-endpoint', ['baz' => 'qux']);

        Http::assertPosted('test-endpoint', ['baz' => 'qux']);

        $this->assertEquals(['foo' => 'bar'], $response);
    }

}
