<?php

namespace Shooorov\Generator\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PillarType extends Model
{
    use HasFactory;

    protected $appends = [
        'migration',
        'validation',
        'max_n_min_length',
        'full_n_float_length',
    ];

    /**
     * Get the max_n_min_length.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function migration(): Attribute
    {
        $migration = ["'amount'"];
        $migration_length = $this->full_n_float_length;
        if ($migration_length) {
            $migration_length = explode(',', $migration_length);
            $migration = array_merge($migration, $migration_length);
        }
        $migration = implode(', ', array_filter($migration));

        return Attribute::get(fn () => $migration);
    }

    /**
     * Get the max_n_min_length.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function validation(): Attribute
    {
        $validation = ['required', $this->validate];
        $validation_length = $this->max_n_min_length;
        if ($validation_length) {
            $validation_length = explode(',', $validation_length);
            $validation = array_merge($validation, $validation_length);
        }
        $validation = array_map(function ($item) {
            return "'$item'";
        }, $validation);

        $validation = implode(', ', array_filter($validation));

        return Attribute::get(fn () => $validation);
    }

    /**
     * Get the max_n_min_length.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function maxNMinLength(): Attribute
    {
        $array = [
            $this->max_length,
            $this->min_length,
        ];

        return Attribute::get(fn () => implode(', ', array_filter($array)));
    }

    /**
     * Get the full_n_float_length.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function fullNFloatLength(): Attribute
    {
        $array = [
            $this->full_length,
            $this->float_length,
        ];

        return Attribute::get(fn () => implode(', ', array_filter($array)));
    }
}
