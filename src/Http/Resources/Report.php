<?php

namespace App\Http\Resources;

use App\UseDatabase;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class Report extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new ReportCollection($resource), function ($collection) {
            $collection->collects = __CLASS__;
        });
    }

    // Set the keys that are supposed to be filtered out
    public function hide(array $fields)
    {
        $this->withoutFields = $fields;

        return $this;
    }

    // Remove the filtered keys.
    protected function filterFields($array)
    {
        return collect($array)->forget($this->withoutFields)->toArray();
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $tables = UseDatabase::getDatabaseDetails();

        $columns = $tables[$this->table]['columns'];
        $columns_list = $searches_list = [];
        // this loop into single loop need to change
        foreach ($columns as $column => $type) {
            if ($column == 'id' || $type != 'bigint') {
                $name = Str::of("$this->table.$column")->replace('.', ' ')->replace('_', ' ')->title()->value;

                if (in_array($column, $tables[$this->table]['enums'])) {
                    $model_name = $tables[$this->table]['class'];
                    $model = "App\Models\\$model_name";
                    $column_plural = Str::of($column)->plural()->value;
                    $options = (new $model)->$column_plural;
                    foreach ($options as $option => $css) {
                        $capital_option = Str::of($option)->replace('-', ' ')->title()->value;
                        $columns_list[] = ['id' => "$this->table.$column.$option", 'name' => "$name $capital_option"];
                    }
                } else {
                    $columns_list[] = ['id' => "$this->table.$column", 'name' => $name];
                }

                continue;
            }
            $table = Str::of($column)->replace('_id', '')->plural()->value;

            $table_columns = array_key_exists($table, $tables) ? $tables[$table]['columns'] : [];
            foreach ($table_columns as $col => $type) {
                $name = Str::of("$table.$col")->replace('.', ' ')->replace('_', ' ')->title()->value;

                if (in_array($col, $tables[$table]['enums'])) {
                    $model_name = $tables[$table]['class'];
                    $model = "App\Models\\$model_name";
                    $col_plural = Str::of($col)->plural()->value;
                    $options = (new $model)->$col_plural;
                    foreach ($options as $option => $css) {
                        $capital_option = Str::of($option)->replace('-', ' ')->title()->value;
                        $columns_list[] = ['id' => "$table.$col.$option", 'name' => "$name $capital_option"];
                    }
                } else {
                    $columns_list[] = ['id' => "$table.$col", 'name' => $name];
                }
            }
            $table_title = Str::of($table)->singular()->title()->value;
            $table_title = $table_title == 'Creator' ? 'User' : $table_title;

            $searches_list[] = ['id' => "$table", 'name' => $table_title];
        }

        return $this->filterFields([
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'table' => $this->table,
            'serial_no' => $this->serial_no,
            'date_range' => $this->date_range,
            'table_name' => Str::of($this->table)->singular()->title()->value,
            'fields' => $this->fields ? json_decode($this->fields) : [],
            'filters' => $this->filters ? json_decode($this->filters) : [],

            'columns' => $columns_list,
            'searches' => $searches_list,
        ]);
    }
}
