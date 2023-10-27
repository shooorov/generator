<?php

namespace Shooorov\Generator\Http\Controllers;

use App\Helpers;
use App\Http\Controllers\Controller;
use Shooorov\Generator\Http\Resources\Desk as ResourcesDesk;
use Shooorov\Generator\MakeFile;
use Shooorov\Generator\Models\Desk;
use Shooorov\Generator\Models\DeskPillar;
use Shooorov\Generator\Models\PillarType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DeskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $desks = Desk::all();
        $params = [
            'desks' => count($desks) ? ResourcesDesk::collection($desks) : [],
        ];

		return Inertia::render('vendor/Generator/Desk/Index', $params);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function all_tables()
    {
        foreach (Desk::all() as $desk) {
            self::generate_files($desk);
        }

        return back()->with('success', __('All Files Generated successfully'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $desks = Desk::all();
        $params = [
            'desks' => count($desks) ? ResourcesDesk::collection($desks) : [],
            'pillar_types' => PillarType::all(),
        ];

        return Inertia::render('vendor/Generator/Desk/Create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $desk = new Desk;
        $desk->name = $request->name;
        $desk->save();

        DB::commit();

        return redirect()->route('generator.desk.edit', $desk->id)->with('success', __('Desk added successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Desk  $desk
     * @return Response
     */
    public function show(Request $request)
    {
		$desk = Desk::findOrFail($request->desk);

		$params = [
            'desk' => new ResourcesDesk($desk),
        ];

        return Inertia::render('vendor/Generator/Desk/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Desk  $desk
     * @return Response
     */
    public function edit(Request $request)
    {
		$desk = Desk::findOrFail($request->desk);

        $params = [
            'desk' => new ResourcesDesk($desk),
            'desks' => ResourcesDesk::collection(Desk::all()),
            'pillar_types' => PillarType::all(),
        ];

        return Inertia::render('vendor/Generator/Desk/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Desk  $desk
     * @return Response
     */
    public function update(Request $request)
    {
		$desk = Desk::findOrFail($request->desk);

        $request->validate([
            'name' => ['required', 'string', 'max:191'],
        ]);

        DB::beginTransaction();

        $object = $desk;
        $length = 0;
        $previous_pillars = $object->pillars->pluck('id')->toArray();
        foreach ($request->group_pillars ?? [] as $item) {
            if (!$item['pillar_type_id']) {
                continue;
            }
            $length = strlen($item['column']) > $length ? strlen($item['column']) : $length;

            $id = array_key_exists('id', $item) ? $item['id'] : null;
            if (($key = array_search($id, $previous_pillars)) !== false) {
                unset($previous_pillars[$key]);
            }

            DeskPillar::updateOrCreate(
                [
                    'id' => $id,
                    'desk_id' => $object->id,
                ],
                [
                    'title' => $item['title'],
                    'column' => $item['column'],
                    'unique' => $item['unique'],
                    'default' => $item['default'],
                    'indexing' => $item['indexing'],
                    'filtering' => $item['filtering'],
                    'requisite' => $item['requisite'],
                    'column_length' => strlen($item['column']),
                    'pillar_type_id' => $item['pillar_type_id'],
                ]
            );
        }
        DeskPillar::where('desk_id', $object->id)->whereIn('id', $previous_pillars)->delete();
        $length = ceil($length / 4) * 4;

        $object->name = $request->name;
        $object->directory = $request->directory;
        $object->child_table = $request->child_table;
        $object->parent_table = $request->parent_table;

        $object->has_filter = $request->has_filter;
        $object->has_opening = $request->has_opening;
        $object->columns_in_row = $request->columns_in_row;
        $object->has_description = $request->has_description;

        $object->highest_column_length = $length;
        $object->update();

        DB::commit();

        return back()->with('success', __('Desk modified successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Desk  $desk
     * @return Response
     */
    public function decorate(Request $request)
    {
		$desk = Desk::findOrFail($request->desk);

        $params = [
            'desk' => new ResourcesDesk($desk),
        ];

        return Inertia::render('vendor/Generator/Desk/Decorate', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Desk  $desk
     * @return Response
     */
    public function decoration(Request $request)
    {
		$desk = Desk::findOrFail($request->desk);

        DB::beginTransaction();

        $object = $desk;
        $object->has_filter = $request->has_filter ?? $object->has_filter;
        $object->has_opening = $request->has_opening ?? $object->has_opening;
        $object->has_polymorphic = $request->has_polymorphic ?? $object->has_polymorphic;
        $object->has_description = $request->has_description ?? $object->has_description;

        $object->generate_cache = $request->generate_cache;
        $object->generate_pages = $request->generate_pages;
        $object->generate_model = $request->generate_model;
        $object->generate_seeder = $request->generate_seeder;
        $object->generate_migration = $request->generate_migration;
        $object->generate_controller = $request->generate_controller;
        $object->generate_resources = $request->generate_resources;
        $object->update();

        foreach ($request->group_pillars ?? [] as $item) {
            DeskPillar::updateOrCreate(
                [
                    'id' => $item['id'],
                    'desk_id' => $desk->id,
                ],
                [
                    'decorating' => $item['decorating'],
                    'empty_column' => $item['empty_column'],
                    'columns_in_row' => $item['columns_in_row'],
                ]
            );
        }

        DB::commit();

        return back()->with('success', __('Desk Decoration modified successfully'));
    }

    public function generate_files(Desk $desk)
    {
        $desk->generate_cache ? MakeFile::makeCache($desk) : null;
        $desk->generate_pages ? MakeFile::makeVueFiles($desk) : null;
        $desk->generate_model ? MakeFile::makeModel($desk) : null;
        $desk->generate_seeder ? MakeFile::makeSeeder($desk) : null;
        $desk->generate_resources ? MakeFile::makeResources($desk) : null;
        $desk->generate_migration ? MakeFile::makeMigration($desk) : null;
        $desk->generate_controller ? MakeFile::makeController($desk) : null;

        // MakeFile::routeModify($desk);

        return back()->with('success', $desk->name . ' Vue Pages, Migration, Model, Controller Created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Desk  $desk
     * @return Response
     */
    public function destroy(Request $request)
    {
		$desk = Desk::findOrFail($request->desk);

        $record = $desk;
        $strings = Helpers::getStringsFromRecord($record);
        $children = Helpers::getChildrenFromModel($record);
        $record->children = $children;

        $params = [
            'record' => $record,
            'strings' => $strings,
        ];

        return Inertia::render('Delete', $params);
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param  Desk  $desk
     * @return Response
     */
    public function confirm_destroy(Request $request)
    {
		$desk = Desk::findOrFail($request->desk);

        $record = $desk;
        $name = class_basename($record);
        $children = Helpers::getChildrenFromModel($record);

        DB::beginTransaction();

        foreach ($children as $child) {
            foreach ($child['records'] as $child_record) {
                $child_record->delete();
            }
        }
        $record->delete();

        DB::commit();

        return redirect()->route('generator.desk.index')->with('fail', __($name . ' removed successfully.'));
    }
}
