<?php

namespace Shooorov\Generator\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Desk extends JsonResource
{
    protected $withoutFields = [];

    public static function collection($resource)
    {
        return tap(new DeskCollection($resource), function ($collection) {
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
        $route = Str::of($this->name)->slug('_');
        $route_list = false;

        if (Route::has($route.'.index')) {
            $route_list = [
                'index' => route($route.'.index'),
                'create' => route($route.'.create'),
                'store' => route($route.'.store'),
                'show' => Route::has($route.'.show') ? route($route.'.show', $this->id) : '#',
                'edit' => route($route.'.edit', $this->id),
                'update' => route($route.'.update', $this->id),
                'destroy' => route($route.'.destroy', $this->id),
            ];
        }
        // dd(route($route . '.index'));
        $pillars = $this->pillars()->orderBy('decorating')->get();

        return $this->filterFields([
            'id' => $this->id,
            'name' => $this->name,
            'directory' => $this->directory,
            'child_table' => $this->child_table,
            'parent_table' => $this->parent_table,
            'columns_in_row' => $this->columns_in_row,
            'highest_column_length' => $this->highest_column_length,

            'generate_cache' => $this->generate_cache ? true : false,
            'generate_pages' => $this->generate_pages ? true : false,
            'generate_model' => $this->generate_model ? true : false,
            'generate_seeder' => $this->generate_seeder ? true : false,
            'generate_migration' => $this->generate_migration ? true : false,
            'generate_controller' => $this->generate_controller ? true : false,
            'generate_resources' => $this->generate_resources ? true : false,

            'class' => $this->class,
            'object' => $this->object,
            'name_plural' => $this->name_plural,
            'object_plural' => $this->object_plural,

            'has_filter' => $this->has_filter ? true : false,
            'has_opening' => $this->has_opening ? true : false,
            'has_polymorphic' => $this->has_polymorphic ? true : false,
            'has_description' => $this->has_description ? true : false,
            'has_remark' => $this->has_remark ? true : false,
            'has_soft_deletes' => $this->has_soft_deletes ? true : false,

            'route' => $route_list,
            'group_pillars' => count($pillars) ? DeskPillar::collection($pillars) : [],
        ]);
    }
}
