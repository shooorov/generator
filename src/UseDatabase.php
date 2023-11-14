<?php

namespace Shooorov\Generator;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class UseDatabase
{
    public static function tables()
    {
        $database = env('DB_DATABASE');
        $tables = array_column(Schema::getAllTables(), "Tables_in_$database");
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        return $tables;
    }

    public static function filterTables()
    {
        $database_tables = self::tables();
        foreach ($database_tables as $table) {
            $default_structure = ['class' => null, 'object' => null, 'enums' => [], 'columns' => [], 'hasMany' => [], 'belongsTo' => []];

            $default_structure['class'] = Helpers::getStringStudly($table);
            $default_structure['object'] = Helpers::getStringSingular($table);
            $columns = [];
            foreach (Schema::getColumnListing($table) as $column) {
                $columns[$column] = DB::getSchemaBuilder()->getColumnType($table, $column);
            }
            $default_structure['columns'] = $columns;

            $tables[$table] = $default_structure;
        }

        return $tables;
    }

    public static function tablesFromDatabase()
    {
        // self::createDatabaseTxt();

        $database_tables = self::tables();
        $tables = [];
        foreach ($database_tables as $table) {
            $default_structure = ['class' => null, 'object' => null, 'enums' => [], 'columns' => [], 'hasMany' => [], 'belongsTo' => []];

            $default_structure['class'] = Helpers::getStringStudly($table);
            $default_structure['object'] = Helpers::getStringSingular($table);
            $columns = [];
            foreach (Schema::getColumnListing($table) as $column) {
                $columns[$column] = DB::getSchemaBuilder()->getColumnType($table, $column);
            }
            $default_structure['columns'] = $columns;

            $tables[$table] = $default_structure;
        }

        return $tables;
    }

    public static function tablesFromDatabaseTxt()
    {
        $file_path = base_path('files\Generator\database.txt');
        if (! file_exists($file_path)) {
            self::createDatabaseTxt();
        }
        $database_tables = file($file_path);
        $tables = [];
        foreach ($database_tables as $key => $line) {
            $table_columns = explode('=', str_replace("\n", '', $line));
            $table = $table_columns[0];
            $columns_list = explode(',', $table_columns[1]);
            $columns = [];
            foreach ($columns_list as $column) {
                $filed = explode('-', $column);
                $columns[$filed[0]] = $filed[1];
            }
            $tables[$table] = ['class' => null, 'object' => null, 'enums' => [], 'columns' => [], 'hasMany' => [], 'belongsTo' => []];
            $tables[$table]['class'] = Helpers::getStringStudly($table);
            $tables[$table]['object'] = Helpers::getStringSingular($table);
            $tables[$table]['columns'] = $columns;
        }

        return $tables;
    }

    public static function createDatabaseTxt()
    {
        $database_tables = self::tables();
        $tables = [];
        foreach ($database_tables as $table) {
            $table_columns = Schema::getColumnListing($table);
            $columns = [];
            foreach ($table_columns as $column) {
                $columns[] = $column.'-'.DB::getSchemaBuilder()->getColumnType($table, $column);
            }

            $tables[] = $table.'='.implode(',', $columns);
        }

        $content = implode("\n", $tables);
        $file_path = base_path('files\Generator\database.txt');
        Helpers::makeFile($content, $file_path ?? public_path());
    }

    public static function getDatabaseDetails()
    {
        $ignore_models = [
            'FailedJob',
            'History',
            'Notification',
            'PasswordReset',
            'PersonalAccessToken',
            'Action',
            'Status',
            'LeaveStatus',
            'LeaveAuthority',
            'RequisitionStatus',
            'RequisitionAuthority',
            'Organization',
            'Description',
            'Migration',
            'Unit',
        ];
        $filter_models = [
            'Account',
            'Asset',
            'AssetCategory',
            'Client',
            'Department',
            'Designation',
            'Document',
            'Image',
            'Item',
            'ItemCategory',
            'Product',
            'ProductItem',
            'Receive',
            'Role',
            'Setting',
            'Signature',
            'Supplier',
            'Transaction',
            'UnitConversion',
            'User',
        ];
        $model_default_object_keys = [
            'connection',
            'table',
            'primaryKey',
            'keyType',
            'incrementing',
            'with',
            'withCount',
            'preventsLazyLoading',
            'perPage',
            'exists',
            'wasRecentlyCreated',
            'escapeWhenCastingToString',
            'attributes',
            'original',
            'changes',
            'casts',
            'classCastCache',
            'attributeCastCache',
            'dates',
            'dateFormat',
            'appends',
            'dispatchesEvents',
            'observables',
            'relations',
            'touches',
            'timestamps',
            'hidden',
            'visible',
            'fillable',
            'guarded',
            'forceDeleting',
        ];
        $ignore_columns = ['created_at', 'updated_at', 'deleted_at'];

        $file_path = base_path('files\Generator\database.txt');
        $database_tables = file_exists($file_path) ? UseDatabase::tablesFromDatabaseTxt() : UseDatabase::tablesFromDatabase();

        $tables = $filter_tables = [];
        foreach ($database_tables as $table => $array) {
            // !in_array($model_table, $ignore_models) ? $tables[$table] = $table : null;
            in_array($array['class'], $filter_models) ? $tables[$table] = $array : null;
            in_array($array['class'], $filter_models) ? $filter_tables[$table] = $array : null;
        }

        foreach ($filter_tables as $table => $array) {
            $model_name = $array['class'];
            $file_path = base_path("app\Models\\$model_name.php");
            if (! file_exists($file_path) || in_array($table, $ignore_models)) {
                continue;
            }

            foreach ($array['columns'] as $column => $type) {
                if (in_array($column, $ignore_columns)) {
                    continue;
                }

                $use_column = Str::of($column)->replace('_id', '')->plural()->value;
                if (array_key_exists($use_column, $database_tables) || in_array($use_column, array_keys($database_tables))) {
                    array_push($database_tables[$use_column]['hasMany'], $table);
                }

                if (str_contains($column, '_id')) {
                    array_push($database_tables[$table]['belongsTo'], $use_column);
                }

                $model = "App\Models\\$model_name";
                if (! in_array($use_column, $model_default_object_keys) && property_exists(new $model, $use_column)) {
                    array_push($database_tables[$table]['enums'], $column);
                }
            }
        }

        return $database_tables;
    }
}
