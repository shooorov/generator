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

class GeneratorSeeder2 extends Seeder
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
            // 'Table' => [
            //     ['title' => 'Directory Name', 'type' => 'string'],
            //     ['title' => 'Has Address', 'type' => 'tinyInteger'],
            //     ['title' => 'Has Description', 'type' => 'tinyInteger'],
            //     ['title' => 'Resource Files', 'type' => 'tinyInteger'],
            // ],

            // 'Column Type' => [
            //     ['title' => 'Name', 'type' => 'string', 'requisite' => true],
            //     ['title' => 'Validation', 'type' => 'string', 'requisite' => true],
            //     ['title' => 'Length', 'type' => 'string'],
            // ],

            // 'Table Column' => [
            //     ['title' => 'Title', 'type' => 'string', 'requisite' => true],
            //     ['title' => 'Column', 'type' => 'string', 'requisite' => true],
            //     ['title' => 'Default', 'type' => 'string'],
            //     ['title' => 'Unique', 'type' => 'tinyInteger', 'requisite' => true],
            //     ['title' => 'Nullable', 'type' => 'tinyInteger', 'requisite' => true],
            //     ['title' => 'Index Position', 'type' => 'string', 'requisite' => true],
            //     ['title' => 'Table Id', 'type' => 'foreignId', 'requisite' => true],
            //     ['title' => 'Column Type Id', 'type' => 'foreignId', 'requisite' => true],
            // ],

            // 'Check Type' => [
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'columns_in_row' => 2, 'requisite' => true],
            // ],

            // 'Check' => [
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Operator', 'type' => 'string'],
            //     ['title' => 'Depreciation', 'type' => 'decimal', 'default' => 0],
            //     ['title' => 'Value', 'type' => 'decimal', 'indexing' => '5', 'requisite' => true, 'default' => 0],
            //     ['title' => 'Is Fixed', 'type' => 'tinyInteger', 'indexing' => '4', 'default' => 0],
            //     ['title' => 'Status', 'type' => 'string'],
            //     ['title' => 'Sale Date', 'type' => 'date'],
            //     ['title' => 'Purchase Date', 'type' => 'date', 'indexing' => '1'],
            //     ['title' => 'Opening Date', 'type' => 'date'],
            //     ['title' => 'Opening Balance', 'type' => 'decimal', 'default' => 0],
            //     ['title' => 'Check Type Id', 'type' => 'foreignId', 'indexing' => '3', 'requisite' => true],
            // ],

            // 'Check Item' => [
            //     ['title' => 'Check Id', 'type' => 'foreignId', 'requisite' => true],
            //     ['title' => 'Number', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Amount', 'type' => 'decimal', 'indexing' => '3', 'requisite' => true],
            //     ['title' => 'Description', 'type' => 'longText', 'indexing' => '2', 'requisite' => true],
            // ],

            // 'Report' => [
            //     ['title' => 'Title', 'type' => 'string', 'indexing' => '1', 'columns_in_row' => 2, 'requisite' => true],
            //     ['title' => 'Table', 'type' => 'string', 'indexing' => '2', 'columns_in_row' => 2, 'requisite' => true],
            //     ['title' => 'Type', 'type' => 'enum', 'indexing' => '3', 'columns_in_row' => 2, 'default' => 'none, yearly, monthly'],
            //     ['title' => 'Filters', 'type' => 'json'],
            //     ['title' => 'Fields', 'type' => 'json'],
            // ],

            // 'Product Category' => [
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'HS Code', 'type' => 'string'],
            //     ['title' => 'Brand', 'type' => 'string'],
            //     ['title' => 'Color', 'type' => 'string'],
            //     ['title' => 'Model Year', 'type' => 'string'],
            //     ['title' => 'Vat Type', 'type' => 'string', 'default' => 'include, exclude, fixed'],
            //     ['title' => 'Purchase Type', 'type' => 'string', 'default' => 'import, local, both'],
            //     ['title' => 'Vat', 'type' => 'integer'],
            //     ['title' => 'SD', 'type' => 'integer'],
            //     ['title' => 'AT', 'type' => 'integer'],
            //     ['title' => 'CD', 'type' => 'integer'],
            //     ['title' => 'RD', 'type' => 'integer'],
            //     ['title' => 'AIT', 'type' => 'integer'],
            //     ['title' => 'TTI', 'type' => 'integer'],
            //     ['title' => 'Exd', 'type' => 'integer'],
            //     ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '2'],
            // ],

            // 'Port' => [
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Code', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            // ],

            // 'Organization' => [
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'System Name', 'type' => 'string', 'requisite' => true],
            //     ['title' => 'Short', 'type' => 'string'],
            //     ['title' => 'Address', 'type' => 'string', 'indexing' => '1'],
            //     ['title' => 'Logo', 'type' => 'string',],
            //     ['title' => 'Membership Date', 'type' => 'date',],
            //     ['title' => 'Membership Expire Date', 'type' => 'date',],

            //     ['title' => 'Name Of Entry', 'type' => 'string'],
            //     ['title' => 'Email', 'type' => 'string'],
            //     ['title' => 'Password', 'type' => 'string'],
            //     ['title' => 'Username', 'type' => 'string'],
            //     ['title' => 'Trust Code', 'type' => 'string'],
            //     ['title' => 'Phone', 'type' => 'string'],
            //     ['title' => 'BIN', 'type' => 'string'],
            //     ['title' => 'TIN', 'type' => 'string'],

            //     ['title' => 'Contact Person Name', 'type' => 'string'],
            //     ['title' => 'Contact Person Phone', 'type' => 'string'],
            //     ['title' => 'Contact Person Designation', 'type' => 'string'],

            //     ['title' => 'Owner', 'type' => 'string'],
            //     ['title' => 'Owner Address', 'type' => 'string'],
            //     ['title' => 'Owner Phone', 'type' => 'string'],
            //     ['title' => 'Ownership Type', 'type' => 'string'],

            //     ['title' => 'Vat Office Address', 'type' => 'string'],
            //     ['title' => 'Alt Phone', 'type' => 'string'],
            //     ['title' => 'Effective Date', 'type' => 'date'],
            //     ['title' => 'Status', 'type' => 'string', 'default' => 'active, inactive'],

            //     ['title' => 'Pad Heading Color', 'type' => 'string'],
            //     ['title' => 'Pad Attachment', 'type' => 'string'],
            //     ['title' => 'View Own Product Material', 'type' => 'tinyInteger', 'default' => false],
            // ],

            // 'Organization Payment' => [
            //     ['title' => 'Date', 'type' => 'date', 'empty_column' => 'after', 'indexing' => '1'],
            //     ['title' => 'Organization Id', 'type' => 'foreignId', 'table' => 'Organization', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Payment Method Id', 'type' => 'foreignId', 'table' => 'Payment Method', 'indexing' => '3', 'requisite' => true],
            //     ['title' => 'Plan Package Id', 'type' => 'foreignId', 'table' => 'Plan Package', 'indexing' => '4', 'requisite' => true],
            //     ['title' => 'Amount', 'type' => 'integer', 'indexing' => '5', 'requisite' => true],
            //     ['title' => 'Discount Amount', 'type' => 'integer', 'indexing' => '6', 'requisite' => true],
            //     ['title' => 'Start Date', 'type' => 'date', 'indexing' => '7', 'requisite' => true],
            // ],

            // 'Vat Four Point Three' => [
            //     ['title' => 'Purchase Id', 'type' => 'foreignId', 'table' => 'Purchase', 'indexing' => '3', 'requisite' => true],
            //     ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '2', 'requisite' => true],

            //     ['title' => 'Date', 'type' => 'date', 'empty_column' => 'after', 'indexing' => '1'],
            //     ['title' => 'Pad Date', 'type' => 'date'],
            //     ['title' => 'Product Service Details', 'type' => 'string'],
            //     ['title' => 'Chassis Or Description', 'type' => 'string'],

            //     ['title' => 'Purchase Quantity', 'type' => 'decimal'],
            //     ['title' => 'Base Price', 'type' => 'decimal'],
            //     ['title' => 'SD', 'type' => 'decimal'],

            //     ['title' => 'Assessable', 'type' => 'string'],
            //     ['title' => 'With Depreciation Amount', 'type' => 'string'],
            //     ['title' => 'Depreciation Amount', 'type' => 'string'],
            //     ['title' => 'Depreciation in Percent', 'type' => 'string'],

            //     ['title' => 'CNF', 'type' => 'string'],
            //     ['title' => 'Bank Interest', 'type' => 'string'],
            //     ['title' => 'Maintenance', 'type' => 'string'],
            //     ['title' => 'Insurance', 'type' => 'string'],
            //     ['title' => 'Rent', 'type' => 'string'],
            //     ['title' => 'Salary', 'type' => 'string'],
            //     ['title' => 'Driver', 'type' => 'string'],
            //     ['title' => 'Port Bill', 'type' => 'string'],
            //     ['title' => 'Others', 'type' => 'string'],
            //     ['title' => 'Profit', 'type' => 'string'],
            //     ['title' => 'Total', 'type' => 'string'],
            //     ['title' => 'Probable Market Price', 'type' => 'string'],
            //     ['title' => 'Amendment Comment', 'type' => 'string'],

            //     ['title' => 'Unit Id', 'type' => 'foreignId', 'table' => 'Unit', 'indexing' => '3', 'requisite' => true],
            // ],

            // 'Product' => [
            //     ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Gender', 'type' => 'string', 'indexing' => '3', 'default' => 'male, female, common'],
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Color', 'type' => 'string'],
            //     ['title' => 'Weight', 'type' => 'decimal'],
            //     ['title' => 'Horn', 'type' => 'integer'],
            //     ['title' => 'Teeth', 'type' => 'integer'],
            //     ['title' => 'Length', 'type' => 'decimal'],
            //     ['title' => 'Height', 'type' => 'decimal'],
            //     ['title' => 'Previous Owner History', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            // 'Disease' => [
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'columns_in_row' => 2, 'requisite' => true],
            // ],

            // 'Disease Cure' => [
            //     ['title' => 'Disease Id', 'type' => 'foreignId', 'table' => 'Disease', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Item Id', 'type' => 'foreignId', 'table' => 'Item', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Quantity', 'type' => 'integer'],
            //     ['title' => 'Duration', 'type' => 'string',],
            //     ['title' => 'Dose', 'type' => 'string',],
            // ],

            // 'Checkup' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Growth', 'type' => 'string', 'default' => 'okay, not-okay'],
            //     ['title' => 'Horn', 'type' => 'integer'],
            //     ['title' => 'Teeth', 'type' => 'integer'],
            //     ['title' => 'Length', 'type' => 'decimal'],
            //     ['title' => 'Height', 'type' => 'decimal'],
            //     ['title' => 'Weight', 'type' => 'decimal', 'indexing' => '3'],
            //     ['title' => 'Inspection', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            // 'Affect' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Checkup Id', 'type' => 'foreignId', 'table' => 'Checkup'],
            //     ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Disease Id', 'type' => 'foreignId', 'table' => 'Disease', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Start Date', 'type' => 'date'],
            //     ['title' => 'End Date', 'type' => 'date'],
            //     ['title' => 'Inspection', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            // 'Conjugation' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Status', 'type' => 'string', 'indexing' => '3', 'default' => 'not-yet, success, failed'],
            //     ['title' => 'Confirm Date', 'type' => 'date'],
            //     ['title' => 'Expected Date', 'type' => 'date'],
            //     ['title' => 'Delivery Date', 'type' => 'date'],
            //     ['title' => 'Inspection', 'type' => 'longText', 'columns_in_row' => 2],
            //     ['title' => 'Checkup Id', 'type' => 'foreignId', 'table' => 'Checkup'],
            //     ['title' => 'Male Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '3', 'requisite' => true],
            //     ['title' => 'Female Id', 'type' => 'foreignId', 'table' => 'Product', 'requisite' => true],
            // ],

            // 'Defect' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'empty_column' => 'after', 'requisite' => true],
            //     ['title' => 'Checkup Id', 'type' => 'foreignId', 'table' => 'Checkup'],
            //     ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Inspection', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            // 'Feed' => [
            //     ['title' => 'Date', 'type' => 'date', 'requisite' => true],
            //     ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Item Id', 'type' => 'foreignId', 'table' => 'Item', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Quantity', 'type' => 'integer'],
            //     ['title' => 'Inspection', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            // 'Vaccine' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Checkup Id', 'type' => 'foreignId', 'table' => 'Checkup'],
            //     ['title' => 'Affect Id', 'type' => 'foreignId', 'table' => 'Affect', 'indexing' => '3', 'requisite' => true],
            //     ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Item Id', 'type' => 'foreignId', 'table' => 'Item', 'indexing' => '4', 'requisite' => true],
            //     ['title' => 'Next Date', 'type' => 'date'],
            //     ['title' => 'Dose', 'type' => 'string',],
            //     ['title' => 'Inspection', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            // 'Item' => [
            //     ['title' => 'Item Category Id', 'type' => 'foreignId', 'table' => 'Item Category', 'empty_column' => 'after', 'requisite' => true],
            //     ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'columns_in_row' => 2, 'requisite' => true],
            //     ['title' => 'Company', 'type' => 'string'],
            //     ['title' => 'Price', 'type' => 'decimal', 'indexing' => '2'],
            //     ['title' => 'Quantity', 'type' => 'decimal'],
            //     ['title' => 'Unit Id', 'type' => 'foreignId', 'table' => 'Unit', 'indexing' => '3'],
            //     ['title' => 'Instruction', 'type' => 'longText', 'columns_in_row' => 2],
            // ],

            // 'Routine' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '2'],
            //     ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '3'],
            // ],

            // 'Routine Item' => [
            //     ['title' => 'Date', 'type' => 'date', 'requisite' => true],
            //     ['title' => 'Item Id', 'type' => 'foreignId', 'table' => 'Item', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Quantity', 'type' => 'decimal', 'indexing' => '2', 'requisite' => true],
            //     ['title' => 'Unit Id', 'type' => 'foreignId', 'table' => 'Unit'],
            //     ['title' => 'Description', 'type' => 'longText'],
            // ],

            // 'Item Inventory' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Total', 'type' => 'decimal', 'indexing' => '2'],
            // ],

            // 'Item Inventory Item' => [
            //     ['title' => 'Date', 'type' => 'date', 'indexing' => '1', 'requisite' => true],
            //     ['title' => 'Item Id', 'type' => 'foreignId', 'table' => 'Item', 'indexing' => '3', 'requisite' => true],
            //     ['title' => 'Unit Id', 'type' => 'foreignId', 'table' => 'Unit'],
            //     ['title' => 'Quantity', 'type' => 'decimal', 'indexing' => '4', 'requisite' => true],
            //     ['title' => 'Price', 'type' => 'decimal', 'indexing' => '5'],
            //     ['title' => 'Total', 'type' => 'decimal', 'indexing' => '6'],
            //     ['title' => 'Manufacturing Date', 'type' => 'date'],
            //     ['title' => 'Expire Date', 'type' => 'date'],
            // ],

            'Value Addition' => [
                ['title' => 'Name', 'type' => 'string', 'indexing' => '1', 'requisite' => true],
            ],

            'Vat Four Point Three' => [
                ['title' => 'Date', 'type' => 'date', 'empty_column' => 'after', 'indexing' => '1'],
                ['title' => 'Pad Date', 'type' => 'date'],
                ['title' => 'Product Service Details', 'type' => 'string'],
                ['title' => 'Chassis Or Description', 'type' => 'string'],

                ['title' => 'Purchase Quantity', 'type' => 'decimal'],
                ['title' => 'Base Price', 'type' => 'decimal'],
                ['title' => 'SD', 'type' => 'decimal'],

                ['title' => 'Assessable', 'type' => 'string'],
                ['title' => 'With Depreciation Amount', 'type' => 'string'],
                ['title' => 'Depreciation Amount', 'type' => 'string'],
                ['title' => 'Depreciation in Percent', 'type' => 'string'],

                ['title' => 'CNF', 'type' => 'string'],
                ['title' => 'Bank Interest', 'type' => 'string'],
                ['title' => 'Maintenance', 'type' => 'string'],
                ['title' => 'Insurance', 'type' => 'string'],
                ['title' => 'Rent', 'type' => 'string'],
                ['title' => 'Salary', 'type' => 'string'],
                ['title' => 'Driver', 'type' => 'string'],
                ['title' => 'Port Bill', 'type' => 'string'],
                ['title' => 'Others', 'type' => 'string'],
                ['title' => 'Profit', 'type' => 'string'],
                ['title' => 'Total', 'type' => 'string'],
                ['title' => 'Probable Market Price', 'type' => 'string'],
                ['title' => 'Amendment Comment', 'type' => 'string'],

                ['title' => 'Unit Id', 'type' => 'foreignId', 'table' => 'Unit', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Purchase Id', 'type' => 'foreignId', 'table' => 'Purchase', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '2', 'requisite' => true],
            ],

            'Vat Six Point One' => [
                ['title' => 'Date', 'type' => 'date', 'empty_column' => 'after', 'indexing' => '1'],
                ['title' => 'Opening Quantity', 'type' => 'decimal'],
                ['title' => 'Purchase Quantity', 'type' => 'decimal'],
                ['title' => 'Base Price', 'type' => 'decimal'],
                ['title' => 'Vat', 'type' => 'decimal'],
                ['title' => 'SD', 'type' => 'decimal'],

                ['title' => 'Bill Of Entry', 'type' => 'string', 'comment' => 'Bill of entry or Invoice No'],
                ['title' => 'Seller Info', 'type' => 'string', 'comment' => 'Seller Name, Address, NID, TIN, BIN'],

                ['title' => 'Unit Id', 'type' => 'foreignId', 'table' => 'Unit', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Purchase Id', 'type' => 'foreignId', 'table' => 'Purchase', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '2', 'requisite' => true],
                ['title' => 'Manufacture Id', 'type' => 'foreignId', 'table' => 'Manufacture', 'indexing' => '2', 'requisite' => true],
                ['title' => 'Vat Four Point Three Id', 'type' => 'foreignId', 'table' => 'Vat Four Point Three', 'indexing' => '2', 'requisite' => true],
            ],

            'Vat Six Point Two' => [
                ['title' => 'Date', 'type' => 'date', 'empty_column' => 'after', 'indexing' => '1'],
                ['title' => 'Opening Quantity', 'type' => 'decimal'],
                ['title' => 'Purchase Quantity', 'type' => 'decimal'],
                ['title' => 'Base Price', 'type' => 'decimal'],
                ['title' => 'Vat', 'type' => 'decimal'],
                ['title' => 'SD', 'type' => 'decimal'],

                ['title' => 'Bill Of Entry', 'type' => 'string', 'comment' => 'Bill of entry or Invoice No'],
                ['title' => 'Seller Info', 'type' => 'string', 'comment' => 'Seller Name, Address, NID, TIN, BIN'],

                ['title' => 'Unit Id', 'type' => 'foreignId', 'table' => 'Unit', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Product Id', 'type' => 'foreignId', 'table' => 'Product', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Purchase Id', 'type' => 'foreignId', 'table' => 'Purchase', 'indexing' => '3', 'requisite' => true],
                ['title' => 'Product Category Id', 'type' => 'foreignId', 'table' => 'Product Category', 'indexing' => '2', 'requisite' => true],
                ['title' => 'Manufacture Id', 'type' => 'foreignId', 'table' => 'Manufacture', 'indexing' => '2', 'requisite' => true],
                ['title' => 'Vat Four Point Three Id', 'type' => 'foreignId', 'table' => 'Vat Four Point Three', 'indexing' => '2', 'requisite' => true],
            ],
        ];

        $table = [
            'Value Addition' => ['directory' => 'System'],
            'Vat Four Point Three' => ['directory' => 'Vat'],
            'Vat Six Point One' => ['directory' => 'Vat', 'has_description' => 1],
            'Vat Six Point Two' => ['directory' => 'Vat'],
            'Vat Six Point Three' => ['directory' => 'Vat'],
            'Vat Six Point Four' => ['directory' => 'Vat'],
            'Vat Six Point Five' => ['directory' => 'Vat'],
            'Vat Six Point Six' => ['directory' => 'Vat'],
            'Vat Six Point Seven' => ['directory' => 'Vat'],
            'Vat Six Point Eight' => ['directory' => 'Vat'],
            'Vat Six Point Ten' => ['directory' => 'Vat'],
            'Vat Nine Point One' => ['directory' => 'Vat'],
            'Vat Nine Point Two' => ['directory' => 'Vat'],
            'Vat Nine Point Three' => ['directory' => 'Vat'],
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
