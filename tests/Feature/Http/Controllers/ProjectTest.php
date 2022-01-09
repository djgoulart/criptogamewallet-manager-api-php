<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class ProjectTest extends TestCase
{
    use RefreshDatabase, TestValidations, TestSaves;

    private $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project = Project::factory()->create();
    }

    public function test_invalidation_data()
    {
        $data = ['name' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');

        $data = ['name' => str_repeat('a', 256)];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

        Project::factory()->create(['name' => 'test1']);
        $this->assertInvalidationInStoreAction(['name' => 'test1'], 'unique');
        $this->assertInvalidationInUpdateAction(['name' => 'test1'], 'unique');

        $data = ['url' => 'http:/invalid_url'];
        $this->assertInvalidationInStoreAction($data, 'url');
        $this->assertInvalidationInUpdateAction($data, 'url');

        $data = ['network' => 'testnet'];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');

        $data = ['is_active' => 'a'];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function test_index()
    {
        /** @var TestResponse $response */
        $response = $this->json('GET', route('projects.index'));

        $response
            ->assertOk()
            ->assertJson([$this->project->toArray()]);
    }

    public function test_show()
    {
        /** @var TestResponse $response */
        $response = $this->json('GET', route(
            'projects.show',
            ['project' => $this->project->id]
        ));

        $response
            ->assertOk()
            ->assertJson($this->project->toArray());
    }

    public function test_store()
    {
        $data = Project::factory()->make()->toArray();
        $response = $this->assertStore(
            $data,
            $data + ['is_active' => true, 'deleted_at' => null]
        );
        $response->assertJsonStructure([
            'created_at', 'updated_at'
        ]);

        $data = Project::factory()->make(['is_active' => false])->toArray();
        $this->assertStore(
            $data,
            $data + ['is_active' => false]
        );
    }

    public function test_update()
    {
        $response = $this->json(
            'PUT',
            route('projects.update', ['project' => $this->project->id]),
            [
                'name' => 'updated name',
                'url' => 'http://updated.url',
                'network' => 'eth',
            ]
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'updated name',
                'url' => 'http://updated.url',
                'network' => 'eth',
            ]);
    }

    public function test_delete()
    {
        $project = Project::factory()->create();
        $response = $this->json(
            'DELETE',
            route(
                'projects.destroy',
                ['project' => $project->id]
            )
        );

        $this->assertSoftDeleted($project);
        $response->assertOk();
    }

    protected function routeStore()
    {
        return route('projects.store');
    }

    protected function routeUpdate()
    {
        return route('projects.update', ['project' => $this->project->id]);
    }

    protected function model()
    {
        return Project::class;
    }
}
