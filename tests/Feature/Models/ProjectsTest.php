<?php

namespace Tests\Feature\Models;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function test_model_list_projects()
    {
        Project::factory(2)->create();
        $projects = Project::all();
        $projectKeys = array_keys($projects->first()->getAttributes());

        $this->assertCount(2, $projects);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'url',
                'network',
                'is_active',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            $projectKeys
        );
    }

    /**
     * @test
     */
    public function test_model_create_one_project()
    {
        $project = Project::create([
            'name' => 'Test project',
            'network' => 'bsc',
        ]);

        $project->refresh();
        $this->assertEquals(36, strlen($project->id));
        $this->assertEquals('Test project', $project->name);
        $this->assertEquals('bsc', $project->network);
        $this->assertNull($project->url);
        $this->assertTrue($project->is_active);
        $this->assertIsString($project->id);


        $project2 = Project::create([
            'name' => 'Test project2',
            'url' => null,
            'network' => 'bsc',
        ]);

        $this->assertNull($project2->url);


        $project3 = Project::create([
            'name' => 'Test project3',
            'url' => 'http://proj.url',
            'network' => 'bsc',
        ]);

        $this->assertEquals('http://proj.url', $project3->url);


        $project4 = Project::create([
            'name' => 'Test project3',
            'network' => 'bsc',
            'is_active' => false,
        ]);

        $project4->refresh();
        $this->assertFalse($project4->is_active);
    }


    /**
     * @test
     */
    public function test_model_update_a_project()
    {
        $project = Project::factory()->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'Test1',
            'url' => 'http://test.url',
            'is_active' => true
        ];

        $project->update($data);
        $project->refresh();

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $project->{$key});
        }
    }

    /**
     * @test
     */
    public function test_model_soft_delete_a_project()
    {
        $project = Project::factory()->create();
        $project->delete();

        $this->assertNull(Project::find($project->id));

        $project->restore();
        $this->assertNotNull(Project::find($project->id));
    }
}
