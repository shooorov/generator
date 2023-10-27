<?php

namespace Shooorov\Generator\Http\Controllers;

use App\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Shooorov\Generator\Models\Pillar;
use Shooorov\Generator\Models\PillarType;

class PillarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $params = [
            'pillars' => Pillar::all(),
        ];

        return Inertia::render('vendor/Generator/Pillar/Index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $params = [
            'pillar_types' => PillarType::all(),
        ];

        return Inertia::render('vendor/Generator/Pillar/Create', $params);
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
            'pillar_type_id' => ['required', 'exists:pillar_types,id'],
        ]);

        DB::beginTransaction();
        $pillar = new Pillar;
        $pillar->name = $request->name;
        $pillar->pillar_type_id = $request->pillar_type_id;
        $pillar->save();

        DB::commit();

        return redirect()->route('generator.pillar.index')->with('success', __('Pillar added successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  Pillar  $pillar
     * @return Response
     */
    public function show(Request $request)
    {
		$pillar = Pillar::findOrFail($request->pillar);

		$params = [
            'pillar' => $pillar,
        ];

        return Inertia::render('vendor/Generator/Pillar/Show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Pillar  $pillar
     * @return Response
     */
    public function edit(Request $request)
    {
		$pillar = Pillar::findOrFail($request->pillar);

        $params = [
            'pillar' => $pillar,
            'pillar_types' => PillarType::all(),
        ];

        return Inertia::render('vendor/Generator/Pillar/Edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Pillar  $pillar
     * @return Response
     */
    public function update(Request $request)
    {
		$pillar = Pillar::findOrFail($request->pillar);

        $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'pillar_type_id' => ['required', 'exists:pillar_types,id'],
        ]);

        DB::beginTransaction();
        $pillar->name = $request->name;
        $pillar->pillar_type_id = $request->pillar_type_id;
        $pillar->update();

        DB::commit();

        return back()->with('success', __('Pillar modified successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Pillar  $pillar
     * @return Response
     */
    public function destroy(Request $request)
    {
		$pillar = Pillar::findOrFail($request->pillar);

        $record = $pillar;
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
     * @param  Pillar  $pillar
     * @return Response
     */
    public function confirm_destroy(Request $request)
    {
		$pillar = Pillar::findOrFail($request->pillar);

        $record = $pillar;
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
