<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Tests\Traits\TestValidations;

class ProjectTest extends TestCase
{
    use RefreshDatabase, TestValidations;


    protected function assertInvalidationFieldRequired(TestResponse $response)
    {
        $this->assertInvalidationFields(
            $response,
            ['name', 'url', 'network'],
            'required'
        );
    }

    protected function assertInvalidationFieldMax(TestResponse $response)
    {
        $this->assertInvalidationFields(
            $response,
            ['name'],
            'max.string',
            ['max' => 255]
        );
    }

    protected function assertInvalidationFieldUnique(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'unique');
    }

    protected function assertInvalidationFieldBoolean(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['is_active'], 'boolean');
    }

    public function test_invalidation_data()
    {
        $response = $this->json('POST', route('projects.store'), []);
        $this->assertInvalidationFieldRequired($response);

        $response = $this->json('POST', route('projects.store'), [
            'name' => str_repeat('a', 256)
        ]);
        $this->assertInvalidationFieldMax($response);

        $project = Project::factory()->create(['name' => 'test1']);
        $response = $this->json('POST', route('projects.store'), [
            'name' => 'test1',
        ]);
        $this->assertInvalidationFieldUnique($response);

        $project = Project::factory()->make(['url' => 'http//invalid_url']);
        $response = $this->json('POST', route('projects.store'), $project->attributesToArray());
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['url'])
            ->assertJsonFragment([
                Lang::get('validation.url', ['attribute' => 'url'])
            ]);

        $projectData = [
            'name' => 'test1',
            'url' => 'http://test1.com',
            'network' => 'testnet'
        ];
        $response = $this->json('POST', route('projects.store'), $projectData);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['network'])
            ->assertJsonFragment([
                Lang::get('validation.in', ['attribute' => 'network'])
            ]);

        $projectData = [
            'name' => 'test1',
            'url' => 'http://test1.com',
            'network' => 'testnet',
            'is_active' => 'test'
        ];
        $response = $this->json('POST', route('projects.store'), $projectData);
        $this->assertInvalidationFieldBoolean($response);

        $project = Project::factory()->create();
        $project2 = Project::factory()->create();
        $response = $this->json(
            'PUT',
            route('projects.update', ['project' => $project2->id]),
            ['name' => $project->name]
        );
        $this->assertInvalidationFieldUnique($response);

        $response = $this->json(
            'PUT',
            route('projects.update', ['project' => $project->id]),
            ['is_active' => 'false']
        );
        $this->assertInvalidationFieldBoolean($response);
    }

    public function test_project_index()
    {
        $project = Project::factory()->create();

        /** @var TestResponse $response */
        $response = $this->json('GET', route('projects.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$project->toArray()]);
    }

    public function test_project_show()
    {
        /** @var Project */
        $project = Project::factory()->create();

        /** @var TestResponse $response */
        $response = $this->json('GET', route(
            'projects.show',
            ['project' => $project->id]
        ));

        $response
            ->assertStatus(200)
            ->assertJson($project->toArray());
    }

    public function test_project_store()
    {
        $project = Project::factory()->make();
        $response = $this->json('POST', route('projects.store'), $project->attributesToArray());

        $id = $response->json('id');
        $createdProj = Project::find($id);

        $response
            ->assertCreated()
            ->assertJson($createdProj->toArray());
    }

    public function test_project_update()
    {
        $project = Project::factory()->create();
        $response = $this->json(
            'PUT',
            route(
                'projects.update',
                ['project' => $project->id]
            ),
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

    public function test_project_delete()
    {
        $project = Project::factory()->create();
        $response = $this->json('DELETE', route('projects.destroy', ['project' => $project->id]));

        $this->assertSoftDeleted($project);
        $response->assertOk();
    }
}
