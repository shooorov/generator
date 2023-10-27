<?php

namespace Shooorov\Generator\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shooorov\Generator\Helpers;

class Desk extends Model
{
    use HasFactory;

    protected $appends = [
        'class',
        'object',
        'address',
        'filtering',
        'namespace',
        'date_param',
        'name_plural',
        'belongs_to_list',
        'description',
        'remark',
        'param_object',
        'opening_check',
        'has_many_list',
        'casts_array',
        'appends_array',
        'model_enums_array',
        'filter_watch',
        'object_plural',
        'index_columns',
        'filter_columns',
        'description_vue',
        'remark_vue',
        'foreign_columns',
        'filterable_form',
        'resource_columns',
        'filterable_setup',
        'string_changeable',
        'filterable_params',
        'param_object_plural',
    ];

    public function pillars()
    {
        return $this->hasMany(DeskPillar::class);
    }

    /**
     * Get the class.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function class(): Attribute
    {
        $class = Str::of($this->name)->studly()->value;

        return Attribute::get(fn () => $class);
    }

    /**
     * Get the object.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function object(): Attribute
    {
        $object = Str::of($this->name)->snake()->value;

        return Attribute::get(fn () => $object);
    }

    /**
     * Get the namespace.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function namespace(): Attribute
    {
        $directory = ($this->directory ? '\\' . ucfirst($this->directory) : '');
        $namespace = "namespace App\Http\Controllers$directory;";

        return Attribute::get(fn () => $namespace);
    }

    /**
     * Get the namespace.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filtering(): Attribute
    {
        $filtering = [];
        $filtering[] = '$___OBJECT_PLURAL___ = ___CLASS___::latest();';
        if ($this->has_filter) {
            $filtering = [
                '$___OBJECT_PLURAL___ = ___CLASS___::latest();',
                '___FILTERING_ARRAY___',
                "\$___OBJECT_PLURAL___ = \$request->end_date ? \$___OBJECT_PLURAL___->where('date', '<=', now()->parse(\$request->end_date)->format('Y-m-d')) : \$___OBJECT_PLURAL___;",
                "\$___OBJECT_PLURAL___ = \$request->start_date ? \$___OBJECT_PLURAL___->where('date', '>=', now()->parse(\$request->start_date)->format('Y-m-d')) : \$___OBJECT_PLURAL___;",
            ];
        }
        $filtering[] = '$___OBJECT_PLURAL___ = $___OBJECT_PLURAL___->get();';
        $filtering = implode("\n        ", $filtering);

        return Attribute::get(fn () => $filtering);
    }

    /**
     * Get the object.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filterWatch(): Attribute
    {
        $watch = '';
        if ($this->has_filter) {
            $watch = file_get_contents(base_path('files\Generator\stubs\Pages\Index-Watch.stub'));
        }

        return Attribute::get(fn () => $watch);
    }

    /**
     * Get the object.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function openingCheck(): Attribute
    {
        $content = '';
        if ($this->has_opening) {
            $content = file_get_contents(base_path('files\Generator\stubs\ModelOpening.stub'));
        }

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the namePlural.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function stringChangeable(): Attribute
    {
        $pillars = $this->pillars->filter(function ($pillar) {
            return $pillar->type->name == 'foreignId';
        });
        $changeable_strings = Setting::where('type', 'hidden')->pluck('name')->toArray();

        $status = false;
        foreach ($pillars as $pillar) {
            $column = str_replace('_id', '', $pillar->column);
            if (in_array($column, $changeable_strings)) {
                $status = true;
                break;
            }
        }

        return Attribute::get(fn () => $status);
    }

    /**
     * Get the namePlural.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function namePlural(): Attribute
    {
        $name_plural = Str::of($this->name)->plural()->value;

        return Attribute::get(fn () => $name_plural);
    }

    /**
     * Get the objectPlural.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function objectPlural(): Attribute
    {
        $object = Str::of($this->name)->slug('_')->value;
        $object_plural = Str::plural($object);

        return Attribute::get(fn () => $object_plural);
    }

    /**
     * Get the dateParam.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dateParam(): Attribute
    {
        $date_param = '';
        $date_column = $this->pillars()->where('title', 'Date')->first();
        if ($date_column) {
            $length = Helpers::getSpaces($this->highest_column_length, strlen('date'));
            $date_param = "'date' $length=> date('Y-m-d'),";
        }

        return Attribute::get(fn () => $date_param);
    }

    /**
     * Get the address.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function address(): Attribute
    {
        if ($this->has_address) {
            $column = 'Helpers::makeAddress($request->address, $___OBJECT___);';
        } else {
            $column = '';
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the address.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filterableForm(): Attribute
    {
        $content = '';
        if ($this->has_filter) {
            $content = file_get_contents(base_path('files\Generator\stubs\Pages\FilterForm.stub'));
        }

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the address.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filterableSetup(): Attribute
    {
        $setup = '';
        if ($this->has_filter) {
            $setup =
                "const form = reactive({
                    ___FILTER_COLUMNS_ARRAY___
                })

                function submit() {
                    router.get(route('___OBJECT___.index'), form);
                }";
        }

        return Attribute::get(fn () => $setup);
    }

    /**
     * Get the address.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filterableParams(): Attribute
    {
        $params = '';
        if ($this->has_filter) {
            $params = "
            'filter' => [
                ___FILTER_COLUMNS_ARRAY___
            ]";
        }

        return Attribute::get(fn () => $params);
    }

    /**
     * Get the description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function description(): Attribute
    {
        if ($this->has_description) {
            $column = '        UseRecord::makeDescription($request->description, $___OBJECT___);';
        } else {
            $column = '';
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the description.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function descriptionVue(): Attribute
    {
        $content = null;
        if ($this->has_description) {
            $content = file_get_contents(base_path('files\Generator\stubs\Pages\Inputs/text.stub'));

            $content = str_replace('___COLUMN_REQUISITE___', '', $content);
            $content = str_replace('___VUE_CLASS_CSS___', 'sm:col-span-6', $content);
            $content = str_replace('___COLUMN___', 'description', $content);
            $content = str_replace('___TITLE___', 'Description', $content);
        }

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the remark.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function remark(): Attribute
    {
        if ($this->has_remark) {
            $column = 'UseRecord::makeRemark($request->remark, $___OBJECT___);';
        } else {
            $column = '';
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the remark.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function remarkVue(): Attribute
    {
        $content = null;
        if ($this->has_remark) {
            $content = file_get_contents(base_path('files\Generator\stubs\Pages\Inputs/text.stub'));

            $content = str_replace('___COLUMN_REQUISITE___', '', $content);
            $content = str_replace('___VUE_CLASS_CSS___', 'sm:col-span-6', $content);
            $content = str_replace('___COLUMN___', 'remark', $content);
            $content = str_replace('___TITLE___', 'Remark', $content);
        }

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the foreign Columns.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filterColumns(): Attribute
    {
        $columns = $this->pillars()->whereNotNull('filtering')->orderBy('filtering')->get();

        return Attribute::get(fn () => count($columns) ? $columns : []);
    }

    /**
     * Get the indexColumns.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function indexColumns(): Attribute
    {
        $columns = $this->pillars()->whereNotNull('indexing')->orderBy('indexing')->get();

        return Attribute::get(fn () => count($columns) ? $columns : []);
    }

    /**
     * Get the foreign Columns.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function foreignColumns(): Attribute
    {
        $columns = $this->pillars->filter(function ($pillar) {
            return $pillar->type->name == 'foreignId';
        });

        return Attribute::get(fn () => $columns);
    }

    /**
     * Get the paramObject.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function paramObject(): Attribute
    {
        $param = $this->generate_resources ? 'new Resources___CLASS___($___OBJECT___)' : '$___OBJECT___';

        return Attribute::get(fn () => $param);
    }

    /**
     * Get the paramObjectPlural.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function paramObjectPlural(): Attribute
    {
        $param = $this->generate_resources ? 'count($___OBJECT_PLURAL___) ? Resources___CLASS___::collection($___OBJECT_PLURAL___) : []' : '$___OBJECT_PLURAL___';

        return Attribute::get(fn () => $param);
    }

    /**
     * Get the resource Columns.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function resourceColumns(): Attribute
    {
        $columns = [];

        $length = Helpers::getSpaces($this->highest_column_length, strlen('description'));
        $columns[] = $this->has_description ? "'description' $length=> \$this->description" : null;
        $length = Helpers::getSpaces($this->highest_column_length, strlen('remark'));
        $columns[] = $this->has_remark ? "'remark' $length=> \$this->remark" : null;
        $length = Helpers::getSpaces($this->highest_column_length, strlen('group_items'));
        $columns[] = $this->child_table ? "'group_items' $length=> count(\$this->items)? \$this->items : []" : null;

        return Attribute::get(fn () => $columns);
    }

    /**
     * Get the resource belongs_to_list.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function belongsToList(): Attribute
    {
        $belongs_to_array = [];
        $pillars = $this->pillars->filter(function ($pillar) {
            return $pillar->type->name == 'foreignId';
        })->values();

        foreach ($pillars as $pillar) {
            $method_name = Helpers::getStringsFromString($pillar->column)['object'];
            $table = Desk::find($pillar->table_id);
            if (!$table) {
                $table = (object) Helpers::getStringsFromString($pillar->column);
            }
            $content = file_get_contents(base_path('files\Generator\stubs\BelongsTo.stub'));
            $content = str_replace('___CLASS___', $table->class, $content);
            $content = str_replace('___OBJECT___', $method_name, $content);
            $content = str_replace('___PARENT_CLASS___', $this->class, $content);
            if ($method_name != $table->object) {
                $content = str_replace('::class', "::class, '$pillar->column'", $content);
            }
            $belongs_to_array[] = $content;
        }
        $belongs_to_array = implode("\n", $belongs_to_array);

        return Attribute::get(fn () => $belongs_to_array);
    }

    /**
     * Get the resource has_many_list.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function hasManyList(): Attribute
    {
        $has_many_array = [];
        foreach (DeskPillar::where('table_id', $this->id)->get() as $pillar) {
            $table = Desk::find($pillar->desk_id);
            $table_from_column = Helpers::getStringsFromString($pillar->column);
            $content = file_get_contents(base_path('files\Generator\stubs\HasMany.stub'));
            if ($this->class != $table_from_column['class']) {
                $content = str_replace('___OBJECT_PLURAL___', $table_from_column['object_plural'], $content);
                $content = str_replace('::class', "::class, '$pillar->column'", $content);
            } else {
                $content = str_replace('___OBJECT_PLURAL___', $table->object_plural, $content);
            }
            $content = str_replace('___CLASS___', $table->class, $content);
            $content = str_replace('___PARENT_CLASS___', $this->class, $content);
            $has_many_array[] = $content;
        }
        $has_many_array = implode("\n", $has_many_array);

        return Attribute::get(fn () => $has_many_array);
    }
}
