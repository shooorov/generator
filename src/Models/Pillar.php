<?php

namespace Shooorov\Generator\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pillar extends Model
{
    use HasFactory;

    protected $appends = [
        'type_name',
        'type_validation',
        'type_length',
    ];

    public function type()
    {
        return $this->belongsTo(PillarType::class, 'pillar_type_id');
    }

    /**
     * Get the Type Name.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function typeName(): Attribute
    {
        return Attribute::get(fn () => $this->type?->name);
    }

    /**
     * Get the Type Length.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function typeLength(): Attribute
    {
        return Attribute::get(fn () => $this->type?->length);
    }

    /**
     * Get the Type Validation.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function typeValidation(): Attribute
    {
        return Attribute::get(fn () => $this->type?->validation);
    }
}
