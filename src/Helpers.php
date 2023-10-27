<?php

namespace Shooorov\Generator;

use Illuminate\Support\Str;

class Helpers
{

    public static function basePath(string $path): string
    {
		return base_path($path);
        // return __DIR__.'/..'.$path;
    }

    public static function makeFile($content, $file_path): void
    {
        $file_directory = dirname($file_path);
        if (!file_exists($file_directory)) {
            mkdir($file_directory, 0777, true);
        }
        $fp = fopen($file_path, 'wb');
        fwrite($fp, $content);
        fclose($fp);
    }

	public static function getSpaces(int $highest_column_length = 20, int $column_length = 10): string
    {
        return '';
        $padding_char = ' ';
        $before_padding = '';
        $padding_length = $highest_column_length - $column_length;

        return Str::padLeft($before_padding, $padding_length, $padding_char);
    }

    public static function getStringsFromString(string $string): array
    {
        $snake = Str::of($string)->lower()->snake();
        if (str_contains($snake, '_id')) {
            $string = Str::of($snake)->replace('_id', '')->replace('_', ' ')->title()->value;
        }

        $class = Str::of($string)->studly()->value;
        $object = Str::of($string)->lower()->snake()->value;
        $object_name = Str::of($object)->append('_name')->value;
        $child_table = Str::of($string)->plural()->value;
        $object_plural = Str::of($object)->plural()->value;
        $name_plural = Str::of($string)->plural()->value;
        $name_singular = Str::of($string)->singular()->value;

        return [
            'name' => $string,
            'class' => $class,
            'object' => $object,
            'object_name' => $object_name,
            'name_plural' => $name_plural,
            'name_singular' => $name_singular,
            'child_table' => $child_table,
            'object_plural' => $object_plural,
        ];
    }

    public static function getChildrenFromDatabase($record): void
    {
        $name = class_basename($record);
        $class = get_class($record);
        $object = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
        $object_id = $object.'_id';
        $object_plural = Str::plural($object);

        $params = [
            'id' => 'id => '.$record->id,
            'name' => 'name => '.$name,
            'class' => 'class => '.$class,
            'object' => 'object => '.$object,
            'object_id' => 'object_id => '.$object_id,
            'object_plural' => 'object_plural => '.$object_plural,
        ];

        print_r(implode('<br>', $params));
        print_r('<br><br>');
        $directory = base_path('database\migrations');
        $directory_files = array_diff(scandir($directory), ['.', '..']);

        $array = [];
        foreach ($directory_files as $file) {
            if (is_dir($file_path = "$directory/$file")) {
                continue;
            }

            $content = file_get_contents($file_path);
            $contain = str_contains($content, $object_id) || str_contains($content, $object_plural);
            $not_self = ! str_contains($content, "Schema::create('$object_plural', function");
            if ($not_self && $contain) {
                $sub_array = [];
                foreach (file($file_path) as $line_index => $line_content) {
                    $before = "('";
                    $after = "', function (Blueprint";
                    if (str_contains($line_content, $after)) {
                        $child = Str::between($line_content, $before, $after);
                        // $child = Str::of($child)->when(!in_array($child, ['leaves', 'receives']), function ($child) {
                        //     return $child->singular();
                        // })->studly();
                        $sub_array[] = $child;
                        $sub_array = array_unique($sub_array);
                    }
                }
                $array[] = $file.' => '.implode('||', $sub_array);
            }
        }

        print_r(implode('<br>', $array));
        dd();
    }

    public static function getChildrenFromMigration($record): void
    {
        $name = class_basename($record);
        $class = get_class($record);
        $object = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
        $object_id = $object.'_id';
        $object_plural = Str::plural($object);

        $params = [
            'id' => 'id => '.$record->id,
            'name' => 'name => '.$name,
            'class' => 'class => '.$class,
            'object' => 'object => '.$object,
            'object_id' => 'object_id => '.$object_id,
            'object_plural' => 'object_plural => '.$object_plural,
        ];

        print_r(implode('<br>', $params));
        print_r('<br><br>');
        $directory = base_path('database\migrations');
        $directory_files = array_diff(scandir($directory), ['.', '..']);

        $array = [];
        foreach ($directory_files as $file) {
            if (is_dir($file_path = "$directory/$file")) {
                continue;
            }

            $content = file_get_contents($file_path);
            $contain = str_contains($content, $object_id) || str_contains($content, $object_plural);
            $not_self = ! str_contains($content, "Schema::create('$object_plural', function");
            if ($not_self && $contain) {
                $sub_array = [];
                foreach (file($file_path) as $line_content) {
                    $before = "('";
                    $after = "', function (Blueprint";
                    if (str_contains($line_content, $after)) {
                        $child = Str::between($line_content, $before, $after);
                        // $child = Str::of($child)->when(!in_array($child, ['leaves', 'receives']), function ($child) {
                        //     return $child->singular();
                        // })->studly();
                        $sub_array[] = $child;
                        $sub_array = array_unique($sub_array);
                    }
                }
                $array[] = $file.' => '.implode('||', $sub_array);
            }
        }

        print_r(implode('<br>', $array));
        dd();
    }

    // public static function relation(): void
    // {
    //     $tables = UseDatabase::getDatabaseDetails();

    //     $rows = [];
    //     $headers = [
    //         '<th style="border: 1px solid black; width: 10%"></th>',
    //         // '<th style="border: 1px solid black; width: 30%">Columns</th>',
    //         // '<th style="border: 1px solid black; width: 20%">Fails</th>',
    //         '<th style="border: 1px solid black; width: 20%">Has Many</th>',
    //         '<th style="border: 1px solid black; width: 20%">Belongs To</th>',
    //     ];
    //     $rows[] = '<tr>'.implode('', $headers).'</tr>';

    //     foreach ($tables as $model => $table) {
    //         $tds = [];
    //         if (count($table['hasMany'])) {
    //             $tds[] = '<th style="border: 1px solid black;">'.$model.'</th>';
    //             // $tds[] = '<td style="border: 1px solid black;">' . implode(', ', $table['columns']) . '</td>';
    //             // $tds[] = '<td style="border: 1px solid black;">' . implode('<br>', $table['fails']) . '</td>';
    //             $tds[] = '<td style="border: 1px solid black;">'.implode(', ', $table['hasMany']).'</td>';
    //             $tds[] = '<td style="border: 1px solid black;">'.implode(', ', $table['belongsTo']).'</td>';

    //             $rows[] = '<tr>'.implode('', $tds).'</tr>';
    //         }
    //     }
    //     $rows = implode('', $rows);

    //     $string = '
    //     <div style="margin: auto; width: 90%;">
    //         <h1 style="text-align:center"> Table Relations </h1>

    //         <table style="border: 1px solid black; margin: auto;">
    //             <tbody>
    //                 '.$rows.'
    //             </tbody>
    //         </table>
    //     </div>';

    //     // return $tables;
    //     // dd($tables);
    //     echo $string;
    //     dd();
    // }

    public static function getDatabaseDiagram(): void
    {
        $directory = base_path('database\migrations');
        $directory_files = array_diff(scandir($directory), ['.', '..']);

        $filter = [
            'organizations',
            'clients',
            'item_categories',
            'items',
        ];
        $tables = [];
        foreach ($directory_files as $file) {
            if (is_dir($file_path = "$directory/$file")) {
                continue;
            }

            $table_open = false;
            $count = 0;
            $dummy_array = [];
            foreach (file($file_path) as $line_index => $line_content) {
                $ignores = [
                    'table->id',
                    'table->timestamps',
                    'table->softDeletes',
                    'table->rememberToken',
                    'dropColumn',
                ];
                $skip = false;
                foreach ($ignores as $ignore) {
                    if (str_contains($line_content, $ignore)) {
                        $skip = true;
                        break;
                    }
                }

                if (! $table_open && (str_contains($line_content, 'Schema::create') ^ str_contains($line_content, 'Schema::table'))) { // getting table
                    $table_open = true;
                    $new_table_before = "Schema::create('";
                    $old_table_before = "Schema::table('";
                    $new_table = str_contains($line_content, $new_table_before);

                    $before = $new_table ? $new_table_before : $old_table_before;
                    $after = "', function (Blueprint";

                    $table = Str::between($line_content, $before, $after);
                }
                if ($table_open && str_contains($line_content, '}')) {
                    $table_open = false;

                    continue;
                }
                if (! $table_open || $skip || ! str_contains($line_content, 'table->') || ! str_contains($line_content, "'")) {
                    continue;
                }

                $key = $count++;
                $column = explode("'", $line_content);
                // dd([
                //     'table_open'    => $table_open,
                //     'file_path'     => $file_path,
                //     'line'          => $line_index + 1,
                //     'content'       => $line_content,
                // ]);

                $type = 'varchar';
                $column = $column[1];
                if (str_contains($line_content, 'foreignId')) {
                    $native_constrained = str_contains($line_content, 'constrained()');
                    $table_from_column = Str::of($column)->replace('_id', '')->plural()->value;
                    $table_from_constrained = Str::between($line_content, "constrained('", "')");
                    $key = $native_constrained ? $table_from_column : $table_from_constrained;
                    $type = 'foreignId';
                    $dummy_array["$column"]['table'] = $key;
                } else {
                }
                // dd($tables, $key, $column);
                $dummy_array["$column"]['type'] = $type;
            }
            if (! in_array($table, $filter)) {
                continue;
            }

            $tables[$table] = $dummy_array;
            // $tables[$table] = array_unique($tables[$table]);
        }

        dd($tables);
        $content = '';
        foreach ($tables as $table => $columns) {
            $start = "Table $table {";
            $end = '}';

            $column_array = [];
            $column_array[] = 'id int';
            foreach ($columns as $key => $column) {
                if ($column['type'] == 'foreignId') {
                    $child = $column['table'];
                    $column_array[] = $key." int [ref: > $child.id]";
                } else {
                    // $column_array[] = $key . ' varchar';
                }
            }
            $content .= $start."\n    ".implode("\n    ", $column_array)."\n".$end."\n\n";
        }

        $file_path = base_path('files\database\db-diagram.io.txt');
        Helpers::makeFile($content, $file_path ?? public_path());
    }

	public static function modelStatusToArray(array $array): array
    {
        $types = [];
        foreach ($array as $key => $value) {
            $key = isset($key) ? $key : $value;
            $types[] = [
                'id' => $key,
                'name' => Str::of($key)->replace('-', ' ')->title()->value,
            ];
        }

        return $types;
    }

    public static function getStringsFromRecord($record): object
    {
        $name = class_basename($record); // default studlyCase
        $class = get_class($record);

        $object = Str::of($name)->snake()->lower()->value;
        $object_id = $object . '_id';
        $object_plural = Str::of($object)->plural()->value;

        $params = [
            'id' => 'id => ' . $record->id,

            'name' => 'name => ' . $name,
            'class' => 'class => ' . $class,

            'object' => 'object => ' . $object,
            'object_id' => 'object_id => ' . $object_id,
            'object_plural' => 'object_plural => ' . $object_plural,
        ];

        return (object) [
            'params' => $params,

            'name' => $name,
            'class' => $class,
            'object' => $object,
            'object_plural' => $object_plural,
        ];
    }

    public static function getChildrenFromModel($record): array
    {
        $strings = self::getStringsFromRecord($record);

        $children = [];
		$file_name = $strings->name;
        $file_path = Helpers::basePath("app/Models/{$file_name}.php");

        $file_in_array = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($file_in_array as $line_index => $line_content) {
            $before = 'hasMany(';
            $after = '::class';
            if (str_contains($line_content, $before)) {
                $child_model = Str::between($line_content, $before, $after);

                $before = 'public function ';
                $after = '()';
                $child_method = Str::between($file_in_array[$line_index - 2], $before, $after);
                $child_list = $record->$child_method ?? [];
                $children[$child_method] = [
                    'name' => Str::of($child_model)->snake()->replace('_', ' ')->title()->value,
                    'count' => count($child_list),
                    'records' => count($child_list) ? $child_list : [],
                ];
            }
        }

        return $children;
    }
}
