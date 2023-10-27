<?php

namespace Shooorov\Generator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Shooorov\Generator\Helpers;
use Shooorov\Generator\Models\PillarType;

class PillarTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $params = [
            'pillar_types' => PillarType::all(),
        ];

        return Inertia::render('vendor/Generator/PillarType/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [];

        return Inertia::render('vendor/Generator/PillarType/Create', $params);
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
            'mimes' => ['nullable', 'string', 'max:191'],
            'guide' => ['nullable', 'string', 'max:191'],
            'validate' => ['nullable', 'string', 'max:191'],
            'max_n_min_length' => ['nullable', 'string', 'max:191'],
            'full_n_float_length' => ['nullable', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $max_n_min = array_filter(explode(',', $request->max_n_min_length));
        $full_n_float = array_filter(explode(',', $request->full_n_float_length));
        $object = new PillarType;

        $object->name = $request->name;
        $object->guide = $request->guide;
        $object->mimes = $request->mimes;
        $object->validate = $request->validate;
        $object->max_length = count($max_n_min) > 0 ? trim($max_n_min[0]) : null;
        $object->min_length = count($max_n_min) > 1 ? trim($max_n_min[1]) : null;
        $object->full_length = count($full_n_float) > 0 ? trim($full_n_float[0]) : null;
        $object->float_length = count($full_n_float) > 1 ? trim($full_n_float[1]) : null;
        $object->save();

        DB::commit();

        return redirect()->route('generator.pillar_type.index')->with('success', __('PillarType added successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  PillarType  $pillar_type
     * @return Response
     */
    public function show(Request $request)
    {
		$pillar_type = PillarType::findOrFail($request->pillar_type);

        $params = [
            'pillar_type' => $pillar_type,
        ];

        return Inertia::render('vendor/Generator/PillarType/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PillarType  $pillar_type
     * @return Response
     */
    public function edit(Request $request)
    {
		$pillar_type = PillarType::findOrFail($request->pillar_type);

        $params = [
            'pillar_type' => $pillar_type,
        ];

        return Inertia::render('vendor/Generator/PillarType/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  PillarType  $pillar_type
     * @return Response
     */
    public function update(Request $request)
    {
		$pillar_type = PillarType::findOrFail($request->pillar_type);

        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'mimes' => ['nullable', 'string', 'max:191'],
            'guide' => ['nullable', 'string', 'max:191'],
            'validate' => ['nullable', 'string', 'max:191'],
            'max_n_min_length' => ['nullable', 'string', 'max:191'],
            'full_n_float_length' => ['nullable', 'string', 'max:191'],
        ]);

        DB::beginTransaction();
        $max_n_min = array_filter(explode(',', $request->max_n_min_length));
        $full_n_float = array_filter(explode(',', $request->full_n_float_length));

        $object = $pillar_type;
        $object->name = $request->name;
        $object->guide = $request->guide;
        $object->mimes = $request->mimes;
        $object->validate = $request->validate;
        $object->max_length = count($max_n_min) > 0 ? trim($max_n_min[0]) : null;
        $object->min_length = count($max_n_min) > 1 ? trim($max_n_min[1]) : null;
        $object->full_length = count($full_n_float) > 0 ? trim($full_n_float[0]) : null;
        $object->float_length = count($full_n_float) > 1 ? trim($full_n_float[1]) : null;
        $object->update();

        DB::commit();

        return back()->with('success', __('PillarType modified successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PillarType  $pillar_type
     * @return Response
     */
    public function destroy(Request $request)
    {
		$pillar_type = PillarType::findOrFail($request->pillar_type);

        $record = $pillar_type;
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
     * @param  PillarType  $pillar_type
     * @return Response
     */
    public function confirm_destroy(Request $request)
    {
		$pillar_type = PillarType::findOrFail($request->pillar_type);

        $record = $pillar_type;
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

        return redirect()->route('generator.pillar.index')->with('fail', __($name . ' removed successfully.'));
    }
}
