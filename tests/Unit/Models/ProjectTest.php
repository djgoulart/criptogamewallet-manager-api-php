<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuid;

class ProjectTest extends TestCase
{
    private $project;

    protected function setUp(): void
    {
        parent::setUp();
        $this->project = new Project();
    }

    public function test_model_fillable_properties()
    {
        $fillable = [
            'name',
            'url',
            'network',
            'is_active',
        ];

        foreach ($fillable as $property) {
            $this->assertContains($property, $this->project->getFillable());
        }

        $this->assertCount(count($fillable), $this->project->getFillable());
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

        foreach ($casts as $cast) {
            $this->assertContains($cast, $this->project->getCasts());
        }

        $this->assertCount(count($casts), $this->project->getCasts());
    }

    public function test_model_dates_properties()
    {
        $dates = [
            'created_at',
            'updated_at',
            'deleted_at'
        ];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->project->getDates());
        }

        $this->assertCount(count($dates), $this->project->getDates());
    }

    public function test_model_not_using_auto_increment_ids()
    {
        $this->assertFalse($this->project->incrementing);
    }
}
