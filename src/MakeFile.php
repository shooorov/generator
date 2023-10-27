<?php

namespace Shooorov\Generator;

use Illuminate\Support\Str;
use Shooorov\Generator\Models\Desk;
use Shooorov\Generator\Models\DeskPillar;

class MakeFile
{
    public static function makeCache($desk)
    {
        $file_path = __DIR__ . '/../stubs/Cache.stub';
        $content = file_get_contents($file_path);
        $content = str_replace('___CLASS___', $desk->class, $content);
        $content = str_replace('___OBJECT___', $desk->object, $content);

        $file_name = $desk->class . '.php';
        $file_path = base_path("app/Http/Cache/Cache{$file_name}");

        Helpers::makeFile($content, $file_path ?? public_path());
    }

    public static function makeVueFiles($desk)
    {
        $table = Helpers::getStringsFromString($desk->name);
        $child = Desk::where('name', $desk->child_table)->first();

        $index_filter_inputs_array = $filter_watch_array = $index_table_head_columns = $index_table_row_columns = $form_input_array =
            $create_params_array = $params_array = $form_column_create_array = $filter_columns_array = $form_column_array = $show_data_array = [];
        $indexing_pillars = DeskPillar::where('desk_id', $desk->id)->whereNotNull('indexing')->orderBy('indexing')->get();

        foreach ($indexing_pillars as $pillar) {
            $index_table_row_columns[] = $pillar->table_rower;
            $index_table_head_columns[] = $pillar->table_header;
        }

        $date_column = $desk->pillars()->where('title', 'Date')->first();
        if ($date_column) {
            $create_params_array[] = 'date: String,';
        }
        if ($desk->string_changeable) {
            // $params_array[] = "string_change: Object,";
            // $create_params_array[] = "string_change: Object,";
        }

        $decorating_pillars = $desk->pillars()->whereNotNull('decorating')->orderBy('decorating')->get();
        foreach ($decorating_pillars as $pillar) {
            $show_data_array[] = $pillar->show_data;
            $form_input_array[] = $pillar->input_field;
            $form_column_array[] = "$pillar->column: props.___OBJECT___.$pillar->column,";
            $filter_columns_array[] = $pillar->filter_vue;
            $params_array[] = $pillar->dependency_param_vue;
            $filter_watch_array[] = $pillar->watch;

            $pillar_column = "$pillar->column: null,";
            if (count(explode(',', $pillar->default)) > 1) {
                $default = explode(',', $pillar->default)[0];
                $pillar_column = "$pillar->column: '$default',";
            } elseif ($pillar->title == 'Date') {
                $pillar_column = 'date: props.date,';
            }
            $form_column_create_array[] = $pillar_column;
            $index_filter_inputs_array[] = $pillar->combo_field;
        }
        $form_input_array[] = $desk->description_vue;
        $form_column_array[] = $desk->has_description ? 'description: props.___OBJECT___.description,' : null;
        $form_column_create_array[] = $desk->has_description ? 'description: null,' : null;
        $form_column_array[] = $desk->has_remark ? 'remark: props.___OBJECT___.remark,' : null;
        $form_column_create_array[] = $desk->has_remark ? 'remark: null,' : null;

        $return_array = ['breadcrumbs,'];
        if ($desk->has_filter) {
            array_push($return_array, 'form,');
            array_push($return_array, 'submit,');
            array_push($params_array, 'filter: Object,');

            array_push($filter_columns_array, 'end_date: props.filter.end_date,');
            array_push($filter_columns_array, 'start_date: props.filter.start_date,');
        }

        if ($child) {
            foreach ($child->pillars()->where('table_id', '!=', $desk->id)->whereNotNull('decorating')->orderBy('decorating')->get() as $pillar) {
                if ($pillar->type->name == 'foreignId') {
                    array_push($params_array, "$pillar->object_plural: Array,");
                }
            }
        }

        $create_params_array = array_merge($create_params_array, $params_array);
        if ($child) {
            array_push($params_array, '___CHILD_OBJECT_PLURAL___: Array,');
        }

        $filter_watch_array = implode(",\n       ", array_filter($filter_watch_array));
        $return_array = implode("\n            ", $return_array);
        $show_data_array = implode("\n            ", $show_data_array);
        $form_input_array = implode("                            \n", array_filter($form_input_array));
        $form_column_array = implode("\n            ", array_filter($form_column_array));
        $index_filter_inputs_array = implode("\n", array_filter($index_filter_inputs_array));
        $params_array = implode("\n        ", array_filter($params_array));
        $create_params_array = implode("\n        ", array_filter($create_params_array));
        $index_table_row_columns = implode('', $index_table_row_columns);
        $index_table_head_columns = implode('', $index_table_head_columns);
        $form_column_create_array = implode("\n            ", array_filter($form_column_create_array));
        $filter_columns_array = implode("\n            ", array_filter($filter_columns_array));

        if ($child) {
            $child_table = Helpers::getStringsFromString($child->name);
            $table_child_head = $table_child_input = $child_column_create_array = [];
            foreach ($child->pillars()->whereNotNull('decorating')->orderBy('decorating')->get() as $pillar) {
                if ($pillar->column != $table['object'] . '_id') {
                    $table_child_head[] = $pillar->child_head;
                    $table_child_input[] = $pillar->child_input;
                    $child_column_create_array[] = "$pillar->column: null,";
                }
            }

            $table_child_head = implode("\n                                        ", $table_child_head);
            $table_child_input = implode("\n                                        ", $table_child_input);
            $child_column_create_array = implode("\n                ", $child_column_create_array);

            $child_table_content = file_get_contents(__DIR__ . '/../stubs/Pages/ChildTable.stub');
            $child_form_content = file_get_contents(__DIR__ . '/../stubs/Pages/ChildForm.stub');
            $child_add_remove_content = file_get_contents(__DIR__ . '/../stubs/Pages/ChildAddRemove.stub');
        }

        $files = ['Create', 'Edit', 'Index', 'Show'];
        foreach ($files as $file) {
            $content = file_get_contents(__DIR__ . '/../stubs/Pages' . '/' . $file . ($desk->child_table && ($file == 'Create' || $file == 'Edit') ? '-With-Child' : '') . '.stub');
            if ($desk->columns_in_row == 3 && ($file == 'Create' || $file == 'Edit')) {
                $content = Str::of($content)->replaceFirst('div class="max-w-xl mx-auto', 'div class="max-w-5xl mx-auto');
            }
            $content = str_replace('___FORM_COLUMN_CREATE_ARRAY___', $form_column_create_array, $content);
            $content = str_replace('___FILTER_FORM___', $desk->filterable_form, $content);
            $content = str_replace('___INDEX_FILTER_INPUTS_ARRAY___', $index_filter_inputs_array, $content);
            $content = str_replace('___FILTER_SETUP___', $desk->filterable_setup, $content);
            $content = str_replace('___FILTER_COLUMNS_ARRAY___', $filter_columns_array, $content);
            $content = str_replace('___RETURN_ARRAY___', $return_array, $content);

            $content = str_replace('___FILTER_WATCH___', $desk->filter_watch, $content);
            $content = str_replace('___FILTER_WATCH_ARRAY___', $filter_watch_array, $content);

            $content = str_replace('___PARAMS_ARRAY___', $file == 'Create' ? $create_params_array : $params_array, $content);
            $content = str_replace('___PROPS_ON___', ($desk->date_param != '' ? 'props' : 'props'), $content);
            $content = str_replace('___FORM_COLUMN_ARRAY___', $form_column_array, $content);
            $content = str_replace('___SHOW_DATA_ARRAY___', $show_data_array, $content);
            $content = str_replace('___FORM_INPUT_FIELD___', $form_input_array, $content);
            $content = str_replace('___INDEX_TABLE_HEAD___', $index_table_head_columns, $content);
            $content = str_replace('___INDEX_TABLE_ROW___', $index_table_row_columns, $content);
            $content = str_replace('___OBJECT_PLURAL___', $table['object_plural'], $content);
            $content = str_replace('___NAME_PLURAL___', $table['name_plural'], $content);
            $content = str_replace('___NAME_SINGULAR___', $table['name_singular'], $content);
            $content = str_replace('___OBJECT___', $table['object'], $content);
            $content = str_replace('___CLASS___', $table['class'], $content);
            $content = str_replace('___NAME___', $desk->name, $content);

            if ($child) {
                $content = str_replace('___CHILD_INPUT_FIELD___', $child_table_content, $content);
                $content = str_replace('___CHILD_FORM_COLUMN_CREATE_ARRAY___', $child_form_content, $content);
                if ($file == 'Create') {
                    $content = str_replace('props.___CHILD_OBJECT_PLURAL___.length? props.___CHILD_OBJECT_PLURAL___ : ', '', $content);
                }
                $content = str_replace('___CHILD_ADD_REMOVE_METHODS___', $child_add_remove_content, $content);
                $content = str_replace('___FORM_COLUMN_CREATE_ARRAY___', $child_column_create_array, $content);
                $content = str_replace('___CHILD_TABLE_HEAD___', $table_child_head, $content);
                $content = str_replace('___CHILD_TABLE_ROW___', $table_child_input, $content);
                $content = str_replace('___CHILD_OBJECT_PLURAL___', $child_table['object_plural'], $content);
                $content = str_replace('___NAME_PLURAL___', $child_table['name_plural'], $content);
                $content = str_replace('___NAME_SINGULAR___', $child_table['name_singular'], $content);
                $content = str_replace('___CHILD_OBJECT___', $child_table['object'], $content);
                $content = str_replace('___CHILD_NAME_PLURAL___', $child_table['name_plural'], $content);
                $content = str_replace('___OBJECT___', $table['object'], $content);
            }

            $file_name = $table['class'] . '/' . $file . '.vue';
            $dir = ($desk->directory ? ucfirst($desk->directory) . '/' : '');
            $file_path = base_path("/resources/js/Pages/{$dir}{$file_name}");
            Helpers::makeFile($content, $file_path ?? public_path());
        }
    }

    public static function makeModel($desk)
    {
        $table = Helpers::getStringsFromString($desk->name);

        $casts_array = $model_enums_array = $traits_array = $traits_class_array = $appends_array = [];
        foreach ($desk->pillars as $pillar) {
            $casts_array[] = $pillar->cast;
            $model_enums_array[] = $pillar->enum_model;
        }

        $traits_array = [
            'HasFactory',
            $desk->has_soft_deletes ? 'SoftDeletes' : null,
            $desk->has_description ? 'DescriptionTrait' : null,
            $desk->has_remark ? 'RemarkTrait' : null,
        ];
        $appends_array = [
            $desk->has_description ? "'description'" : null,
            $desk->has_remark ? "'remark'" : null,
        ];
        $traits_class_array = [
            $desk->has_description ? 'use App\Traits\DescriptionTrait;' : null,
            $desk->has_remark ? 'use App\Traits\RemarkTrait;' : null,
            'use Illuminate\Database\Eloquent\Factories\HasFactory;',
            'use Illuminate\Database\Eloquent\Model;',
            $desk->has_soft_deletes ? 'use Illuminate\Database\Eloquent\SoftDeletes;' : null,
        ];

        $traits_array = count(array_filter($traits_array)) ? implode(', ', array_filter($traits_array)) : null;
        $traits_class_array = count(array_filter($traits_class_array)) ? implode("\n", array_filter($traits_class_array)) : null;

        $casts_array = array_filter($casts_array);
        $appends_array = array_filter($appends_array);
        $model_enums_array = array_values(array_filter($model_enums_array));

        $body_casts_array = $body_appends_array = $body_model_enums_array = null;

        $content = file_get_contents(__DIR__ . '/../stubs/Model.stub');
        if (count($casts_array)) {
            $body_casts_array = file_get_contents(__DIR__ . '/../stubs/Model/Casts.stub');
            $body_casts_array = str_replace('___MODEL_CASTS_ARRAY___', implode("\n        ", $casts_array), $body_casts_array);
        }
        if (count($appends_array)) {
            $body_appends_array = file_get_contents(__DIR__ . '/../stubs/Model/Appends.stub');
            $body_appends_array = str_replace('___MODEL_APPENDS_ARRAY___', implode(",\n        ", $appends_array), $body_appends_array);
        }
        if (count($model_enums_array)) {
            $body_model_enums_array = implode("\n        ", $model_enums_array);
        }

        $combine_relations = [
            $body_casts_array,
            $body_appends_array,
            $body_model_enums_array,
            $desk->opening_check,
            $desk->belongs_to_list,
            $desk->has_many_list,
        ];

        $content = str_replace('___RELATION_ARRAY___', implode("\n", array_filter($combine_relations)), $content);
        $content = str_replace('___MODEL_TRAIT_ARRAY___', $traits_array, $content);
        $content = str_replace('___MODEL_TRAIT_CLASS_ARRAY___', $traits_class_array, $content);
        $content = str_replace('___CLASS___', $table['class'], $content);

        $file_name = $table['class'] . '.php';
        $file_path = base_path("/app/Models/{$file_name}");
        Helpers::makeFile($content, $file_path ?? public_path());
    }

    public static function makeController($desk)
    {
        $table = Helpers::getStringsFromString($desk->name);
        $child = Desk::where('name', $desk->child_table)->first();
        if ($child) {
            $child_table = Helpers::getStringsFromString($child->name);
        }

        $dependency_traits_array = $params_array = $dependency_helpers_array = $object_store_columns_array = $filtering_array =
            $object_modify_columns_array = $store_validation_columns_array = $modify_validation_columns_array = $filter_columns_array =
            $child_dependency_params_array = [];

        $params_array[] = $desk->date_param;
        $unique = false;
        $decorating_pillars = $desk->pillars()->whereNotNull('decorating')->orderBy('decorating')->get();
        foreach ($decorating_pillars as $pillar) {
            $filter_columns_array[] = $pillar->filter;
            $dependency_traits_array[] = in_array($pillar->dependency_trait, $dependency_traits_array) ? null : $pillar->dependency_trait;
            $params_array[] = $pillar->dependency_param;
            $dependency_helpers_array[] = $pillar->dependency_helper;
            $object_store_columns_array[] = $pillar->store;
            $object_modify_columns_array[] = $pillar->modify;
            $store_validation_columns_array[] = $pillar->store_validation;
            $modify_validation_columns_array[] = $pillar->modify_validation;
            $filtering_array[] = $pillar->record_filtering;

            if ($pillar->unique) {
                $unique = true;
            }
        }

        if ($child) {
            $child_dependency_params_array[] = "'" . $child_table['object_plural'] . "' => "  . "$" . $table['object'] . '->' . $child_table['object_plural'] . ',';

            foreach ($child->pillars()->where('table_id', '!=', $desk->id)->whereNotNull('decorating')->orderBy('decorating')->get() as $pillar) {
                if ($pillar->type->name == 'foreignId') {
                    $params_array[] = $pillar->dependency_param;
                    $dependency_helpers_array[] = $pillar->dependency_helper;
                    $dependency_traits_array[] = in_array($pillar->dependency_trait, $dependency_traits_array) ? null : $pillar->dependency_trait;
                }
            }
        }

        array_push($dependency_traits_array, "use App\Models\\" . $table['class'] . ';');
        $child ? array_push($dependency_traits_array, "use App\Models\\" . $child_table['class'] . ';') : null;
        $unique ? array_push($dependency_traits_array, "use Illuminate\Validation\Rule;") : null;
        $desk->has_description ? array_push($dependency_traits_array, "use App\UseRecord;") : null;
        $desk->has_remark ? array_push($dependency_traits_array, "use App\UseRecord;") : null;
        $desk->generate_resources ? array_push($dependency_traits_array, "use App\Http\Resources\\" . $table['class'] . ' as Resources' . $table['class'] . ';') : null;

        array_push($filter_columns_array, "'end_date'          => \$request->end_date ?? '',");
        array_push($filter_columns_array, "'start_date'        => \$request->start_date ?? '',");

        $filter_columns_array = implode("\n                ", array_filter($filter_columns_array));
        $filtering_array = implode("\n        ", array_filter($filtering_array));
        $params_array = implode("\n            ", array_filter($params_array));
        $dependency_traits_array = implode("\n", array_unique(array_filter($dependency_traits_array)));
        $dependency_helpers_array = implode("\n        ", array_filter($dependency_helpers_array));
        $object_store_columns_array = implode("\n        ", $object_store_columns_array);
        $object_modify_columns_array = implode("\n        ", $object_modify_columns_array);
        $child_dependency_params_array = implode("\n            ", $child_dependency_params_array);
        $store_validation_columns_array = implode("\n            ", $store_validation_columns_array);
        $modify_validation_columns_array = implode("\n            ", $modify_validation_columns_array);

        $content = file_get_contents(__DIR__ . '/../stubs/Controller.stub');
        $content = str_replace('___STORE_VALIDATION_ARRAY___', $store_validation_columns_array, $content);
        $content = str_replace('___MODIFY_VALIDATION_ARRAY___', $modify_validation_columns_array, $content);
        $content = str_replace('___OBJECT_STORE_COLUMNS_ARRAY___', $object_store_columns_array, $content);
        $content = str_replace('___OBJECT_MODIFY_COLUMNS_ARRAY___', $object_modify_columns_array, $content);
        $content = str_replace('___DEPENDENCY_TRAITS_ARRAY___', $dependency_traits_array, $content);
        $content = str_replace('___PARAMS_ARRAY___', $params_array, $content);
        $content = str_replace('___CHILD_PARAMS_ARRAY___', $child_dependency_params_array, $content);
        $content = str_replace('___PARAMS_HELPERS_ARRAY___', $dependency_helpers_array, $content);
        $content = str_replace('___PARAM_OBJECT___', $desk->param_object, $content);
        $content = str_replace('___PARAM_OBJECT_PLURAL___', $desk->param_object_plural, $content);
        $content = str_replace('___FILTER_PARAMS___', $desk->filterable_params, $content);

        $content = str_replace('___FILTERING___', $desk->filtering, $content);
        $content = str_replace('___FILTERING_ARRAY___', $filtering_array, $content);

        $content = str_replace('___FILTER_COLUMNS_ARRAY___', $filter_columns_array, $content);
        $content = str_replace('___OBJECT_PLURAL___', $table['object_plural'], $content);
        $content = str_replace('___OBJECT___', $table['object'], $content);
        $content = str_replace('___CLASS___', $table['class'], $content);
        $content = str_replace('___HAS_ADDRESS___', '', $content);
        $content = str_replace('___NAMESPACE___', $desk->namespace, $content);
        $content = str_replace('___DIRECTORY___', ($desk->directory ? ucfirst($desk->directory) : ''), $content);
        $content = str_replace('___HAS_DESCRIPTION___', $desk->description, $content);
        $content = str_replace('___HAS_REMARK___', $desk->remark, $content);

        $child_object_store_columns_array = [];

        if ($child) {
            foreach ($child->pillars()->whereNotNull('decorating')->orderBy('decorating')->get() as $pillar) {
                if ($pillar->column != $table['object'] . '_id') {
                    $child_object_store_columns_array[] = $pillar->child_store;
                }
            }

            $child_object_store_columns_array = implode("\n                    ", array_filter($child_object_store_columns_array));

            $child_store = file_get_contents(__DIR__ . '/../stubs/ChildStore.stub');
            $content = str_replace('___CHILD_STORE___', $child_store, $content);
            $child_update = file_get_contents(__DIR__ . '/../stubs/ChildUpdate.stub');
            $content = str_replace('___CHILD_UPDATE___', $child_update, $content);

            $content = str_replace('___CHILD_OBJECT_STORE_COLUMNS_ARRAY___', $child_object_store_columns_array, $content);
            $content = str_replace('___CHILD_OBJECT_MODIFY_COLUMNS_ARRAY___', $child_object_store_columns_array, $content);
            $content = str_replace('___CHILD_OBJECT_PLURAL___', $child_table['object_plural'], $content);
            $content = str_replace('___CHILD_OBJECT___', $child_table['object'], $content);
            $content = str_replace('___CHILD_CLASS___', $child_table['class'], $content);
        }

        $content = str_replace('___OBJECT___', $table['object'], $content);
        $content = str_replace('___CHILD_STORE___', '', $content);
        $content = str_replace('___CHILD_UPDATE___', '', $content);

        $file_name = $table['class'] . 'Controller.php';
        $dir = ($desk->directory ? ucfirst($desk->directory) . '/' : '');
        $file_path = base_path("/app/Http/Controllers/{$dir}{$file_name}");
        Helpers::makeFile($content, $file_path ?? public_path());
    }

    public static function makeMigration($desk)
    {
        $table = Helpers::getStringsFromString($desk->name);

        $migration_columns_array = [];
        foreach ($desk->pillars as $pillar) {
            $migration_columns_array[] = $pillar->migration;
        }
        $migration_columns_array = implode("\n            ", $migration_columns_array);

        $content = file_get_contents(__DIR__ . '/../stubs/Migration.stub');
        $content = str_replace('___OBJECT_PLURAL___', $table['object_plural'], $content);
        $content = str_replace('___MIGRATION_COLUMN_ARRAY___', $migration_columns_array, $content);
        $content = str_replace('___SOFT_DELETES___', $desk->has_soft_deletes ? '$table->softDeletes();' : '', $content);

        $directory = base_path('database/migrations');
        $files = array_diff(scandir($directory), ['.', '..']);

        foreach ($files as $file) {
            if (str_contains($file, '_create_' . $table['object_plural'] . '_table.php')) {
                $file_found = $file;
                break;
            }
        }

        $file_name = isset($file_found) ? $file_found : implode('_', [
            date('Y_m_d'),
            ((int) date('his0') + $desk->id),
            'create',
            $table['object_plural'],
            'table.php',
        ]);

        $file_path = base_path("/database/migrations/{$file_name}");
        Helpers::makeFile($content, $file_path ?? public_path());
    }

    public static function makeSeeder($desk)
    {
        $table = Helpers::getStringsFromString($desk->name);

        $factory_columns_array = [];
        foreach ($desk->pillars as $pillar) {
            $factory_columns_array[] = $pillar->factory;
        }

        $factory_columns_array = implode("\n            ", array_filter($factory_columns_array));

        $content = file_get_contents(__DIR__ . '/../stubs/Factory.stub');
        $content = str_replace('___CLASS___', $table['class'], $content);
        $content = str_replace('___FACTORY_COLUMNS_ARRAY___', $factory_columns_array, $content);

        $file_name = $table['class'] . 'Factory.php';
        $file_path = base_path("/database/factories/{$file_name}");
        Helpers::makeFile($content, $file_path ?? public_path());

        $content = file_get_contents(__DIR__ . '/../stubs/Seeder.stub');
        $content = str_replace('___CLASS___', $table['class'], $content);
        $file_name = $table['class'] . 'Seeder.php';
        $file_path = base_path("/database/seeders/{$file_name}");
        Helpers::makeFile($content, $file_path ?? public_path());
    }

    public static function makeResources($desk)
    {
        $table = Helpers::getStringsFromString($desk->name);

        $resource_columns = [];
        $spaces = Helpers::getSpaces($desk->highest_column_length, strlen('id'));
        $resource_columns[] = "'id' $spaces => \$this->id";
        foreach ($desk->pillars as $pillar) {
            $resource_columns = array_merge($resource_columns, $pillar->resource);
        }
        $resource_columns = array_merge($resource_columns, $desk->resource_columns);
        $resource_columns = implode(",\n            ", array_filter($resource_columns));

        $content = file_get_contents(__DIR__ . '/../stubs/Resource.stub');
        $content = str_replace('___CLASS___', $table['class'], $content);
        $content = str_replace('___RESOURCE_COLUMNS_ARRAY___', $resource_columns, $content);

        $file_name = $table['class'] . '.php';
        $file_path = base_path("app/Http/Resources/{$file_name}");
        Helpers::makeFile($content, $file_path ?? public_path());

        $content = file_get_contents(__DIR__ . '/../stubs/ResourceCollection.stub');
        $content = str_replace('___CLASS___', $table['class'], $content);

        $file_name = $table['class'] . 'Collection.php';
        $file_path = base_path("app/Http/Resources/{$file_name}");
        Helpers::makeFile($content, $file_path ?? public_path());
    }

    public static function routeModify($desk)
    {
        $table = Helpers::getStringsFromString($desk->name);
        $class_prefix = Str::of($table['object_plural'])->slug('-');

        $content = file_get_contents(__DIR__ . '/../stubs/Route.stub');
        $content = str_replace('___CLASS_PREFIX___', $class_prefix, $content);
        $content = str_replace('___CLASS___', $table['class'], $content);
        $content = str_replace('___OBJECT___', $table['object'], $content);

        if (file_exists(base_path('AAA/routes/web.php'))) {
            $content = file_get_contents(base_path('AAA/routes/web.php')) . $content;
        } else {
            $content = file_get_contents(__DIR__ . '/../stubs/web.stub') . $content;
        }

        $file_path = base_path() . "/AAA/routes/web.php";
        Helpers::makeFile($content, $file_path ?? public_path());

        $content = file_get_contents(base_path('routes/web/generator.php'));
        $file_path = base_path() . "/AAA/routes/web/generator.php";
        Helpers::makeFile($content, $file_path ?? public_path());
    }
}
