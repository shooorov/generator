<?php

namespace Shooorov\Generator;

use Shooorov\Generator\Models\Desk;
use Shooorov\Generator\Models\DeskPillar;
use Shooorov\Generator\Models\Pillar;
use Shooorov\Generator\Models\PillarType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class GeneratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        Desk::truncate();
        PillarType::truncate();
        Pillar::truncate();
        DeskPillar::truncate();
        Schema::enableForeignKeyConstraints();

        // 'requisite' => 'required or nullable',
        // 'validate' => 'string',
        // 'length' => 'max:2024 or min:11',
        // 'unique' => 'string',
        // 'mimes' => 'string',
        // 'confirmed' => 'string',

        $pillar_types = [
            'string' => ['guide' => '', 'validate' => 'string', 'faker' => '$this->faker->name()'],
            'text' => ['guide' => '', 'validate' => 'string', 'faker' => '$this->faker->realText($maxNbChars = 200, $indexSize = 2)'],
            'longText' => ['guide' => '', 'validate' => 'string', 'faker' => '$this->faker->realText($maxNbChars = 200, $indexSize = 3)'],
            'date' => ['guide' => '', 'validate' => 'date', 'faker' => "\$this->faker->date(\$format = 'Y-m-d', \$max = 'now')"],
            'time' => ['guide' => '', 'validate' => 'time', 'faker' => "\$this->faker->time(\$format = 'H:i:s', \$max = 'now')"],
            'integer' => ['guide' => '', 'validate' => 'numeric', 'faker' => '$this->faker->numberBetween($min = 1000, $max = 9000)'],
            'json' => ['guide' => '', 'validate' => 'json', 'faker' => ''],
            'decimal' => [
                'guide' => "\$table->decimal('decimal', 8, 3); => 32145.154",
                'validate' => 'numeric',
                'faker' => '$this->faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = NULL)',
            ],
            'tinyInteger' => ['guide' => '', 'validate' => 'boolean'],
            'foreignId' => ['guide' => '', 'validate' => 'exists:___OBJECT_PLURAL___,id'],
            'enum' => ['guide' => '', 'validate' => 'string', 'faker' => '$this->faker->randomElement($array = array (___ENUM_OPTIONS___))'],
            'image' => [
                'guide' => '',
                'validate' => "'image', 'mimes:jpeg,png,jpg,gif,svg'",
            ],
        ];

        foreach ($pillar_types as $name => $options) {
            PillarType::create([
                'name' => $name,
                'guide' => $options['guide'],
                'validate' => $options['validate'],
                'faker' => array_key_exists('faker', $options) ? $options['faker'] : null,
            ]);
        }

        $desk_n_pillars = [
            // 'Purchase' => [
            //     ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Gender', 'type' => 'string', 'indexing' => '3', 'default' => 'male, female, baby-boy, baby-girl'],
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Age', 'type' => 'string'],
            //     ['title' => 'Color', 'type' => 'string'],
            //     ['title' => 'Horn', 'type' => 'integer'],
            //     ['title' => 'Teeth', 'type' => 'integer'],
            //     ['title' => 'Height', 'type' => 'decimal'],
            //     ['title' => 'Length', 'type' => 'decimal'],
            //     ['title' => 'Weight', 'type' => 'decimal'],
            //     ['title' => 'Milk Capacity', 'type' => 'decimal'],
            //     ['title' => 'Pregnancy Time', 'type' => 'integer'],
            //     ['title' => 'Pictures', 'type' => 'string', 'columns_in_row' => 2],
            //     ['title' => 'Previous Owner History', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            'Report' => [
                ['title' => 'Title', 'type' => 'string', 'indexing' => '1', 'columns_in_row' => 2, 'requisite' => true],
                ['title' => 'Table', 'type' => 'enum', 'indexing' => '2', 'columns_in_row' => 1, 'default' => 'four-point-three, four-point-four, four-point-five'],
                ['title' => 'Type', 'type' => 'enum', 'indexing' => '3', 'columns_in_row' => 2, 'default' => 'none, yearly, monthly'],
                ['title' => 'Filters', 'type' => 'json'],
                ['title' => 'Fields', 'type' => 'json'],
            ],
        ];

        $table = [
            'Report' => ['directory' => 'Report', 'has_soft_deletes' => 1],
        ];

        foreach ($desk_n_pillars as $name => $columns) {
            $desk = Desk::create([
                'name' => $name,
                'directory' => array_key_exists($name, $table) && array_key_exists('directory', $table[$name]) && $table[$name]['directory'] ? $table[$name]['directory'] : 'Operation',
                'child_table' => array_key_exists($name, $table) && array_key_exists('child_table', $table[$name]) && $table[$name]['child_table'] ? $table[$name]['child_table'] : '',
                'parent_table' => array_key_exists($name, $table) && array_key_exists('parent_table', $table[$name]) && $table[$name]['parent_table'] ? $table[$name]['parent_table'] : '',
                'generate_cache' => array_key_exists($name, $table) && array_key_exists('generate_cache', $table[$name]) && $table[$name]['generate_cache'] ? $table[$name]['generate_cache'] : 0,
                'generate_pages' => array_key_exists($name, $table) && array_key_exists('generate_pages', $table[$name]) && $table[$name]['generate_pages'] ? $table[$name]['generate_pages'] : 0,
                'generate_model' => array_key_exists($name, $table) && array_key_exists('generate_model', $table[$name]) && $table[$name]['generate_model'] ? $table[$name]['generate_model'] : 0,
                'generate_seeder' => array_key_exists($name, $table) && array_key_exists('generate_seeder', $table[$name]) && $table[$name]['generate_seeder'] ? $table[$name]['generate_seeder'] : 0,
                'generate_migration' => array_key_exists($name, $table) && array_key_exists('generate_migration', $table[$name]) && $table[$name]['generate_migration'] ? $table[$name]['generate_migration'] : 0,
                'generate_resources' => array_key_exists($name, $table) && array_key_exists('generate_resources', $table[$name]) && $table[$name]['generate_resources'] ? $table[$name]['generate_resources'] : 0,
                'generate_controller' => array_key_exists($name, $table) && array_key_exists('generate_controller', $table[$name]) && $table[$name]['generate_controller'] ? $table[$name]['generate_controller'] : 0,
                'has_filter' => array_key_exists($name, $table) && array_key_exists('has_filter', $table[$name]) && $table[$name]['has_filter'] ? $table[$name]['has_filter'] : 0,
                'has_opening' => array_key_exists($name, $table) && array_key_exists('has_opening', $table[$name]) && $table[$name]['has_opening'] ? $table[$name]['has_opening'] : 0,
                'columns_in_row' => array_key_exists($name, $table) && array_key_exists('columns_in_row', $table[$name]) && $table[$name]['columns_in_row'] ? $table[$name]['columns_in_row'] : 2,
                'has_polymorphic' => array_key_exists($name, $table) && array_key_exists('has_polymorphic', $table[$name]) && $table[$name]['has_polymorphic'] ? $table[$name]['has_polymorphic'] : 0,
                'has_description' => array_key_exists($name, $table) && array_key_exists('has_description', $table[$name]) && $table[$name]['has_description'] ? $table[$name]['has_description'] : 0,
                'has_remark' => array_key_exists($name, $table) && array_key_exists('has_remark', $table[$name]) && $table[$name]['has_remark'] ? $table[$name]['has_remark'] : 0,
                'has_soft_deletes' => array_key_exists($name, $table) && array_key_exists('has_soft_deletes', $table[$name]) && $table[$name]['has_soft_deletes'] ? $table[$name]['has_soft_deletes'] : 0,
            ]);

            $decorating = 0;
            foreach ($columns as $column) {
                $title = $column['title'];
                $filed = Str::of($column['title'])->slug('_');
                $desk_id = $desk->id;
                $default = array_key_exists('default', $column) && $column['default'] ? $column['default'] : null;
                $table_id = array_key_exists('table', $column) && $column['table'] ? Desk::where('name', $column['table'])->first()?->id : null;
                $indexing = array_key_exists('indexing', $column) && $column['indexing'] ? $column['indexing'] : null;
                $filtering = array_key_exists('table', $column) && array_key_exists('filtering', $column) && $column['filtering'] ? $column['filtering'] : null;
                $requisite = array_key_exists('requisite', $column) && $column['requisite'] ? $column['requisite'] : false;
                // $decorating = array_key_exists('decorating', $column) && $column['decorating'] ? $column['decorating'] : null;
                $empty_column = array_key_exists('empty_column', $column) && $column['empty_column'] ? $column['empty_column'] : 'none';
                $columns_in_row = array_key_exists('columns_in_row', $column) && $column['columns_in_row'] ? $column['columns_in_row'] : 1;
                $pillar_type_id = PillarType::where('name', $column['type'])->first()->id;

                DeskPillar::create([
                    'title' => $title,
                    'column' => $filed,
                    'default' => $default,
                    'desk_id' => $desk_id,
                    'table_id' => $table_id,
                    'indexing' => $indexing,
                    'filtering' => $filtering,
                    'requisite' => $requisite,
                    'decorating' => ++$decorating,
                    'empty_column' => $empty_column,
                    'columns_in_row' => $columns_in_row,
                    'pillar_type_id' => $pillar_type_id,
                ]);
            }
        }

        Artisan::call('optimize:clear');
    }
}
