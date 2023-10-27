<?php

namespace Shooorov\Generator\Models;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Shooorov\Generator\Helpers;

class DeskPillar extends Model
{
    use HasFactory;

    /**
     * The DeskPillar's empty_column_options.
     *
     * @var array<string, string>
     */
    public $empty_column_options = ['none', 'after', 'before'];

    /**
     * The DeskPillar's columns_in_row_options.
     *
     * @var array<string, string>
     */
    public $columns_in_row_options = [1, 2];

    protected $appends = [
        'cast',
        'store',
        'watch',
        'filter',
        'modify',
        'spaces',
        'factory',
        'resource',
        'show_data',
        'migration',
        'filter_vue',
        'input_field',
        'combo_field',
        'child_head',
        'child_input',
        'child_store',
        'enum_model',
        'enum_options',
        'table_rower',
        'table_header',
        'string_change',
        'object_plural',
        'record_filtering',
        'dependency_trait',
        'dependency_param',
        'store_validation',
        'modify_validation',
        'dependency_helper',
        'dependency_param_vue',
        'dependency_helper_vue',
    ];

    public function desk()
    {
        return $this->belongsTo(Desk::class);
    }

    public function type()
    {
        return $this->belongsTo(PillarType::class, 'pillar_type_id');
    }

    /**
     * Get the spaces.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function spaces(): Attribute
    {
        return Attribute::get(fn () => null);
        $length = Helpers::getSpaces($this->desk->highest_column_length ?? 50, $this->column_length);

        return Attribute::get(fn () => $length);
    }

    /**
     * Get the cast.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function cast(): Attribute
    {
        $cast = null;
        if ($this->type->name == 'timestamp' || $this->type->name == 'date') {
            $cast = "'$this->column' $this->spaces=> 'datetime',";
        } elseif ($this->type->name == 'json') {
            $cast = "'$this->column' $this->spaces=> 'array',";
        } elseif ($this->type->name == 'tinyInteger' || $this->type->name == 'boolean') {
            $cast = "'$this->column' $this->spaces=> 'boolean',";
        }

        return Attribute::get(fn () => $cast);
    }

    /**
     * Get the string_change.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function stringChange(): Attribute
    {
        $column = $this->title;

        if ($this->type->name == 'foreignId') {
            $column = Str::of($this->column)->replace('_id', '');
            $changeable_strings = Setting::where('type', 'hidden')->pluck('name')->toArray();
            if (in_array($column, $changeable_strings)) {
                $column = '{{ string_change.' . $column . '_singular }}';
            } else {
                $column = Str::of($column)->replace('_', ' ')->ucfirst();
            }
        }

        return Attribute::get(fn () => (string) $column);
    }

    /**
     * Get the object_plural.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function objectPlural(): Attribute
    {
        $column = Str::of($this->column)->replace('_id', '')->plural();

        return Attribute::get(fn () => (string) $column);
    }

    /**
     * Get the input_field.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function inputField(): Attribute
    {
        if (count(explode(',', $this->default)) > 1) {
            $content = file_get_contents(base_path('files\Generator\stubs\Pages\Inputs/foreignId.stub'));
        } else {
            $content = file_get_contents(base_path('files\Generator\stubs\Pages\Inputs/' . $this->type->name . '.stub'));
        }
        $enum_options = $this->type->name == 'enum' ? $this->enum_options : '';
        if ($this->empty_column != 'none') {
            $empty_column = file_get_contents(base_path('files\Generator\stubs\Pages\Inputs/emptyColumn.stub'));
            $content = $this->empty_column == 'after' ? $content . "\n" . $empty_column : $empty_column . "\n" . $content;
        }

        $columns_in_row = 'sm:col-span-' . ($this->desk->columns_in_row == 3 ? 2 : ($this->columns_in_row * 3));
        $content = str_replace('___ENUM_OPTIONS___', $enum_options, $content);
        $content = str_replace('___COLUMN_REQUISITE___', ($this->requisite ? '<span class="text-red-500">*</span>' : ''), $content);
        $content = str_replace('___VUE_CLASS_CSS___', $columns_in_row, $content);
        $content = str_replace('___OBJECT_PLURAL___', $this->object_plural, $content);
        $content = str_replace('___COLUMN___', $this->column, $content);
        $content = str_replace('___TITLE___', $this->string_change, $content);

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the child_input.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function childInput(): Attribute
    {
        $space = "\n                                        ";
        $content = $space . '<td>';

        $numeric = '';
        if (($this->type->name == 'foreignId' && !empty($this->filtering))) {
            $content .= $space . '    <Combobox class="w-full" v-model="group_item.___COLUMN___" :items="___OBJECT_PLURAL___" />';
        } else if ($this->type->name == 'decimal') {
            $content .= $space . '    <input v-model="group_item.___COLUMN___" placeholder="___TITLE___" type="text" ___NUMERIC___ class="block w-full px-2 focus:ring-none focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />';
            $numeric = $this->type->name == "oninput=\"this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');\"";
        } else {
            $content .= $space . '    <input v-model="group_item.___COLUMN___" placeholder="___TITLE___" type="text" class="block w-full px-2 focus:ring-none focus:ring-primary-400 focus:border-primary-400 hover:bg-gray-100 focus:bg-transparent sm:text-sm border-gray-300 rounded" />';
        }
        $content .= $space . '</td>';

        $content = str_replace('___OBJECT_PLURAL___', $this->object_plural, $content);
        $content = str_replace('___NUMERIC___', $numeric, $content);
        $content = str_replace('___COLUMN___', $this->column, $content);
        $content = str_replace('___TITLE___', $this->title, $content);

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the child_head.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function childHead(): Attribute
    {
        $column = '<th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">___TITLE___</th>';
        $column = str_replace('___TITLE___', Str::of($this->title)->replace(' Id', ' ')->value, $column);

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the child_store.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function childStore(): Attribute
    {
        $column = $this->column;
        $column = "'$column' $this->spaces=> \$item['$column'],";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the combo_field.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function comboField(): Attribute
    {
        if (!($this->type->name == 'foreignId' && !empty($this->filtering))) {
            return Attribute::get(fn () => null);
        }

        $content = file_get_contents(base_path('files\Generator\stubs\Pages\Inputs\Combo-Filter.stub'));
        $content = str_replace('___OBJECT_PLURAL___', $this->object_plural, $content);
        $content = str_replace('___COLUMN___', $this->column, $content);
        $content = str_replace('___TITLE___', $this->string_change, $content);

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the record_filtering.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function recordFiltering(): Attribute
    {
        if (!($this->type->name == 'foreignId' && !empty($this->filtering))) {
            return Attribute::get(fn () => null);
        }

        $column = "\$___OBJECT_PLURAL___ = \$request->$this->column ? \$___OBJECT_PLURAL___->where('$this->column', \$request->$this->column) : \$___OBJECT_PLURAL___;";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the filter.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filter(): Attribute
    {
        if (!($this->type->name == 'foreignId' && !empty($this->filtering))) {
            return Attribute::get(fn () => null);
        }

        $column = "'$this->column' $this->spaces=> \$request->$this->column ?? '',";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the factory.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function factory(): Attribute
    {
        if (($this->type->name == 'foreignId')) {
            return Attribute::get(fn () => null);
        }
        $faker = $this->type->faker;
        $column = "'___COLUMN___' $this->spaces=> $faker,";
        $column = str_replace('___COLUMN___', $this->column, $column);

        if (($this->type->name == 'enum')) {
            $enum_array = explode(',', $this->default);
            $enum_array = array_map(function ($option) {
                return "'$option'";
            }, array_filter($enum_array));
            $column = str_replace('___ENUM_OPTIONS___', implode(',', $enum_array), $column);
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the watch.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function watch(): Attribute
    {
        $column = null;
        if ($this->type->name != 'foreignId') {
            return Attribute::get(fn () => $column);
        }

        $column = "'form.$this->column'() {
            this.submit();
        }";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the filter_vue.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function filterVue(): Attribute
    {
        if (!($this->type->name == 'foreignId' && !empty($this->filtering))) {
            return Attribute::get(fn () => null);
        }

        $column = "$this->column: props.filter.$this->column,";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the store.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function store(): Attribute
    {
        $object = $this->desk->object;
        $column = $this->column;
        if ($this->requisite && ($this->type->name == 'timestamp' || $this->type->name == 'date')) {
            $column = "\$$object->$column $this->spaces= \$request->$column ? now()->parse(\$request->$column) : null;";
        } else {
            $column = "\$$object->$column $this->spaces= \$request->$column;";
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the modify.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function modify(): Attribute
    {
        $object = $this->desk->object;
        $column = $this->column;
        if ($this->type->name == 'timestamp' || $this->type->name == 'date') {
            $column = "\$$object->$column $this->spaces= \$request->$column ? now()->parse(\$request->$column) : \$$object->$column;";
        } else {
            $column = "\$$object->$column $this->spaces= \$request->$column;";
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the enum_options.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function enumOptions(): Attribute
    {
        $options = false;
        if ($this->type->name == 'enum') {
            $options_array = explode(',', $this->default);
            $options = [];
            foreach ($options_array as $option) {
                $option_array = explode('-', $option);
                $option_capitalize = implode(' ', array_map(function ($item) {
                    return ucfirst($item);
                }, $option_array));

                $column = explode('-', $this->column);
                $column_capitalize = implode(' ', array_map(function ($item) {
                    return ucfirst($item);
                }, $column));
                $body = file_get_contents(base_path('files\Generator\stubs\Pages\Inputs/enum-option.stub'));
                $body = str_replace('___OPTION___', $option, $body);
                $body = str_replace('___OPTION_TITLE___', $option_capitalize, $body);
                $options[] = "\n$body";
            }
        }
        $options = implode('', $options);

        return Attribute::get(fn () => $options);
    }

    /**
     * Get the enum_model.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function enumModel(): Attribute
    {
        $content = false;
        $options_array = explode(',', $this->default);
        if (count($options_array) > 1) {
            $column_plural = Str::of($this->column)->plural();

            $content = file_get_contents(base_path('files\Generator\stubs\ModelEnum.stub'));

            $options = [];
            foreach ($options_array as $option) {
                $option = trim($option);
                $title = Str::of($option)->replace('-', ' ')->title()->value;
                $length = Helpers::getSpaces(10, strlen($option));
                $options[] = "'$option' $length => '$title',";
            }

            $options = implode("\n        ", $options);
            $content = str_replace('___COLUMN___', $column_plural, $content);
            $content = str_replace('___ENUM_OPTIONS_ARRAY___', $options, $content);
        }

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the resource.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function resource(): Attribute
    {
        $columns = [];
        $param_part = "'___COLUMN___' $this->spaces=> ";
        $column_part = '$this->___COLUMN___';
        if ($this->type->name == 'timestamp' || $this->type->name == 'date') {
            $format = "->format('Y-m-d')";
            $columns[] = $param_part . ($this->requisite ? "$column_part$format" : "$column_part?$format");
            // $columns[] = $param_part . ($this->requisite ? "$column_part?$format" : "$column_part ? $column_part?$format : null");
        } else {
            $columns[] = "$param_part$column_part";
        }
        $columns[0] = str_replace('___COLUMN___', $this->column, $columns[0]);

        if ($this->type->name == 'foreignId') {
            $table = Helpers::getStringsFromString($this->column);
            $object = $table['object'];
            $name = $table['object_name'];
            $spaces = Helpers::getSpaces($this->desk->highest_column_length, strlen($name));
            $columns[] = "'$name' $spaces => \$this->$object?" . '->name';
        }

        return Attribute::get(fn () => $columns);
    }

    /**
     * Get the table_rower.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function tableRower(): Attribute
    {
        $table = Helpers::getStringsFromString($this->desk->name);
        $content = file_get_contents(base_path('files\Generator\stubs\Pages\IndexTableRow.stub'));
        if ($this->type->name == 'foreignId') {
            $content = str_replace('___COLUMN___', Str::of($this->column)->replace('_id', '')->append('_name'), $content);
            $content = str_replace('___OBJECT___', $table['object'], $content);
        } else {
            $content = str_replace('___COLUMN___', $this->column, $content);
            $content = str_replace('___OBJECT___', $table['object'], $content);
        }

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the table_header.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function tableHeader(): Attribute
    {
        $content = file_get_contents(base_path('files\Generator\stubs\Pages\IndexTableHead.stub'));
        $header = Str::of($this->title)->lower()->replace(' id', '')->title()->value;
        if ($this->type->name == 'foreignId') {
            $content = str_replace('___TITLE___', $header, $content);
        } else {
            $content = str_replace('___TITLE___', $header, $content);
        }

        return Attribute::get(fn () => $content);
    }

    /**
     * Get the show_data.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function showData(): Attribute
    {
        $column = "{ name: '$this->title', value: props.___OBJECT___.$this->column },";
        if ($this->type->name == 'foreignId') {
            $table = Helpers::getStringsFromString($this->column);
            $name = $table['name'];
            $object_name = $table['object_name'];
            $column = "{ name: '$name', value: props.___OBJECT___.$object_name },";
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the migration.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function migration(): Attribute
    {
        $enum_array = explode(',', $this->default);
        $default = $enum_array[0] ?? null;
        $enum_array = array_map(function ($option) {
            return "'$option'";
        }, array_filter($enum_array));

        $enum_options = null;
        // $enum_options = count($enum_array) ? "[" . implode(', ', $enum_array) . "]" : null;
        $length = $this->type->length ?? null;

        $default_body = $this->default ? "default('$default')" : null;
        $default_body = $this->type->name == 'decimal' || $this->type->name == 'tinyInteger' ? 'default(0)' : $default_body;
        $nullable_body = $this->requisite || $this->type->name == 'tinyInteger' || $this->type->name == 'decimal' || $default_body != null ? null : 'nullable()';

        $name_enum_length_body = implode(', ', array_filter([
            "'$this->column'",
            $enum_options,
            $length,
        ]));

        $type = $this->type->name == 'enum' ? 'string' : $this->type->name;
        $unique_body = $this->unique ? 'unique()' : null;
        $foreign_body = $this->type->name == 'foreignId' ? 'constrained()' : null;

        $column = implode('->', array_filter([
            '$table',
            "$type($name_enum_length_body)",
            $unique_body,
            $default_body,
            $nullable_body,
            $foreign_body,
        ])) . ';';

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the store_validation.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function storeValidation(): Attribute
    {
        $validate = $this->type->validate;
        if ($this->type->name == 'foreignId') {
            $foreign_object_plural = Str::of($this->column)->replace('_id', '')->plural();
            $validate = str_replace('___OBJECT_PLURAL___', $foreign_object_plural, $validate);
        }
        $mimes = $this->type->name == 'image' ? $this->type->mimes : null;
        $unique = $this->unique ? 'unique:___OBJECT_PLURAL___' : null;
        $requisite = $this->requisite ? 'required' : 'nullable';
        $max = $this->type->max ?? null; //'max:191'
        $min = $this->type->min ?? null; //'min:11'
        if ($this->type->name == 'foreignId' || $this->type->name == 'decimal' || $this->type->name == 'timestamp' || $this->type->name == 'date') {
            $max = null;
            $min = null;
        }

        $column = [$requisite, $validate, $mimes, $unique, $max, $min];
        $column = array_filter($column);
        $column = array_map(function ($column) {
            return "'" . $column . "'";
        }, $column);
        $column = implode(', ', $column);

        $column = "'$this->column' $this->spaces=> [$column],";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the modify_validation.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function modifyValidation(): Attribute
    {
        $validate = $this->type->validate;
        if ($this->type->name == 'foreignId') {
            $foreign_object_plural = Str::of($this->column)->replace('_id', '')->plural();
            $validate = str_replace('___OBJECT_PLURAL___', $foreign_object_plural, $validate);
        }
        $mimes = $this->type->name == 'image' ? $this->type->mimes : null;
        $unique = $this->unique ? "Rule::unique('___OBJECT_PLURAL___')->ignore(\$___OBJECT___)" : null;
        $requisite = $this->requisite ? 'required' : 'nullable';
        $max = $this->type->max ?? null; //'max:191'
        $min = $this->type->min ?? null; //'min:11'
        if ($this->type->name == 'foreignId' || $this->type->name == 'decimal' || $this->type->name == 'timestamp' || $this->type->name == 'date') {
            $validate = ($this->type->name == 'timestamp' || $this->type->name == 'date') ? 'date' : $validate;
            $max = null;
            $min = null;
        }

        $column = [$requisite, $validate, $mimes, $max, $min];
        $column = array_filter($column);
        $column = array_map(function ($column) {
            return "'" . $column . "'";
        }, $column);
        array_push($column, $unique);
        $column = array_filter($column);
        $column = implode(', ', $column);

        $column = "'$this->column' $this->spaces=> [$column],";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the dependency_trait.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dependencyTrait(): Attribute
    {
        $column = null;

        if ($this->type->name == 'foreignId') {
            $table = Helpers::getStringsFromString($this->column);
            $class = $table['class'];
            if ($child_table = Desk::find($this->table_id)) {
                $child_table = Helpers::getStringsFromString($this->column);
                $class = $child_table['class'];
            }
            $column = "use App\Models\\$class;";
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the dependency_param.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dependencyParam(): Attribute
    {
        $table = Helpers::getStringsFromString($this->column);
        $object_plural = $table['object_plural'];
        $column = null;
        if ($this->type->name == 'foreignId') {
            $column = "'$object_plural' $this->spaces=> count(\$$object_plural) ? \$$object_plural : [],";
        } elseif (count(explode(',', $this->default)) > 1) {
            $desk_class = $this->desk->class;
            $column_plural = Str::of($this->column)->plural()->value;
            $column = "'$column_plural' $this->spaces=> Helpers::modelStatusToArray((new $desk_class)->$column_plural),";
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the dependency_helper.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dependencyHelper(): Attribute
    {
        if ($this->type->name != 'foreignId') {
            return Attribute::get(fn () => null);
        }

        $table = Helpers::getStringsFromString($this->column);
        $class = $table['class'];
        if ($child_table = Desk::find($this->table_id)) {
            $child_table = Helpers::getStringsFromString($this->column);
            $class = $child_table['class'];
        }
        $object_plural = $table['object_plural'];
        $column = "\$$object_plural $this->spaces= $class::all();";

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the dependency_param_vue.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dependencyParamVue(): Attribute
    {
        $column = null;
        if ($this->type->name == 'foreignId') {
            $object_plural = Str::of($this->column)->replace('_id', '')->plural();
            $column = "$object_plural: Array,";
        } else if (count(explode(',', $this->default)) > 1) {
            $column_plural = Str::of($this->column)->plural()->value;
            $column = "$column_plural: Array,";
        }

        return Attribute::get(fn () => $column);
    }

    /**
     * Get the dependency_helper_vue.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function dependencyHelperVue(): Attribute
    {
        if ($this->type->name != 'foreignId') {
            return Attribute::get(fn () => null);
        }
        $object_plural = Str::of($this->column)->replace('_id', '')->plural();
        $column = "$object_plural: Array,";

        return Attribute::get(fn () => $column);
    }
}
