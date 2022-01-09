<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait TestSaves
{
    protected function assertStore(array $sendData, array $testDbData, array $testJsonData = null): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json('POST', $this->routeStore(), $sendData);
        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}:\n {$response->content()}");
        }

        $this->assertContentInDatabase($response, $testDbData);
        $this->assertJsonContentResponse($response, $testDbData, $testJsonData);
        return $response;
    }

    protected function assertUpdate(array $sendData, array $testDbData, array $testJsonData = null): TestResponse
    {
        /** @var TestResponse $response */
        $response = $this->json('PUT', $this->routeUpdate(), $sendData);
        if ($response->status() !== 200) {
            throw new \Exception("Response status must be 200, given {$response->status()}:\n {$response->content()}");
        }

        $this->assertContentInDatabase($response, $testDbData);
        $this->assertJsonContentResponse($response, $testDbData, $testJsonData);
        return $response;
    }

    private function assertContentInDatabase(
        TestResponse $response,
        array $testDbData
    ) {
        $model = $this->model();
        $table = (new $model)->getTable();
        $this->assertDatabaseHas($table, $testDbData + ['id' => $response->json('id')]);
    }

    private function assertJsonContentResponse(
        TestResponse $response,
        array $dbDbData,
        array $jsonData = null
    ) {
        $data = $jsonData ?? $dbDbData;
        $response->assertJsonFragment($data + ['id' => $response->json('id')]);
    }
}
