<?php

namespace Shooorov\Generator\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DeskPillar extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new DeskPillarCollection($resource), function ($collection) {
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
        return $this->filterFields([
            'id' => $this->id,
            'title' => $this->title,
            'column' => $this->column,
            'table_id' => $this->table_id,
            'empty_column' => $this->empty_column,
            'column_length' => $this->column_length,
            'columns_in_row' => $this->columns_in_row,

            'default' => $this->default ?? ($this->type->name == 'decimal' ? 0 : ''),
            'unique' => $this->unique ? true : false,
            'requisite' => $this->requisite ? true : false,

            'indexing' => $this->indexing,
            'filtering' => $this->filtering,
            'decorating' => $this->decorating,

            'desk_id' => $this->desk_id,
            'pillar_type_id' => $this->pillar_type_id,
        ]);
    }
}
