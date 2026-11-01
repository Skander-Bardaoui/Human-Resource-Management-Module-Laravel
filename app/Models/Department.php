<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'tag',
        'description',
        'manager_id',
        'specialties',
        'tags',
        'sub_teams',
        'monthly_budget',
        'occupancy',
        'occupancy_label',
        'metric_value',
        'metric_color',
        'specialties_label',
        'code',
        'dot_color',
        'text_color',
        'bar_color',
    ];

    protected $casts = [
        'specialties' => 'array',
        'tags' => 'array',
        'sub_teams' => 'array',
        'monthly_budget' => 'decimal:3',
        'occupancy' => 'integer',
    ];

    public function manager()
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department', 'slug');
    }

    public function activeEmployees()
    {
        return $this->employees()->where('status', '!=', 'inactif');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $pattern = '%'.$term.'%';

        return $query->where(function (Builder $query) use ($pattern) {
            $query->where('name', 'like', $pattern)
                ->orWhere('description', 'like', $pattern)
                ->orWhere('code', 'like', $pattern)
                ->orWhereHas('manager', function (Builder $query) use ($pattern) {
                    $query->where('name', 'like', $pattern)
                        ->orWhere('email', 'like', $pattern);
                });
        });
    }
}
