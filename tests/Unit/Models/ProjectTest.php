<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuid;

class ProjectTest extends TestCase
{

    public function test_model_fillable_properties()
    {
        $project = new Project();
        $fillable = [
            'name',
            'url',
            'network',
            'is_active',
        ];

        foreach ($fillable as $property) {
            $this->assertContains($property, $project->getFillable());
        }

        $this->assertCount(count($fillable), $project->getFillable());
    }

    public function test_model_use_correct_traits()
    {
        $traits = [
            HasFactory::class,
            SoftDeletes::class,
            Uuid::class
        ];

        $projectTraits = array_keys(class_uses(Project::class));

        foreach ($traits as $trait) {
            $this->assertContains($trait, $projectTraits);
        }

        $this->assertCount(count($traits), $projectTraits);
    }

    public function test_model_casts_properties()
    {
        $casts = ['id' => 'string', 'deleted_at' => 'datetime', 'is_active' => 'boolean'];

        $project = new Project();

        foreach ($casts as $cast) {
            $this->assertContains($cast, $project->getCasts());
        }

        $this->assertCount(count($casts), $project->getCasts());
    }

    public function test_model_dates_properties()
    {
        $project = new Project();
        $dates = [
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        foreach ($dates as $date) {
            $this->assertContains($date, $project->getDates());
        }

        $this->assertCount(count($dates), $project->getDates());
    }

    public function test_model_not_using_auto_increment_ids()
    {
        $project = new Project();

        $this->assertFalse($project->incrementing);
    }
}
