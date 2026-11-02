<?php

namespace App\Models;

use App\Enums\Discipline;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title', 'number', 'discipline', 'language', 'year', 'day',
    'context', 'statement', 'correct_alternative',
])]
class Question extends Model
{
    protected function casts(): array
    {
        return ['discipline' => Discipline::class];
    }

    public function alternatives(): HasMany
    {
        return $this->hasMany(Alternative::class)->orderBy('letter');
    }

    public function images(): HasMany
    {
        return $this->hasMany(QuestionImage::class);
    }

    public function scopeAnswerable(Builder $query): Builder
    {
        return $query->whereNotNull('correct_alternative');
    }
}
