<?php

use Shooorov\Generator\Http\Controllers\DeskController;
use Shooorov\Generator\Http\Controllers\PillarController;
use Shooorov\Generator\Http\Controllers\PillarTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('generator')->name('generator.')->group(function () {
    Route::get('/', [DeskController::class, 'index']);

    Route::get('/files', [DeskController::class, 'all_tables'])->name('all_tables'); // generator.all_tables

    Route::name('desk.')->prefix('desks')->group(function () {
        Route::get('/', [DeskController::class, 'index'])->name('index'); // generator.desk.index
        Route::get('/create', [DeskController::class, 'create'])->name('create'); // generator.desk.create
        Route::post('/store', [DeskController::class, 'store'])->name('store'); // generator.desk.store
        Route::get('/{desk}', [DeskController::class, 'show'])->name('show'); // generator.desk.show
        Route::get('/{desk}/edit', [DeskController::class, 'edit'])->name('edit'); // generator.desk.edit
        Route::patch('/{desk}/update', [DeskController::class, 'update'])->name('update'); // generator.desk.update
        Route::get('/{desk}/destroy', [DeskController::class, 'destroy'])->name('destroy'); // generator.desk.destroy
        Route::delete('/{desk}/confirm-delete', [DeskController::class, 'confirm_destroy'])->name('confirm_destroy'); // generator.desk.confirm_destroy

        Route::get('/{desk}/decorate', [DeskController::class, 'decorate'])->name('decorate'); // generator.desk.decorate
        Route::patch('/{desk}/decoration', [DeskController::class, 'decoration'])->name('decoration'); // generator.desk.decoration
        Route::get('/{desk}/generate-files', [DeskController::class, 'generate_files'])->name('generate_files'); // generator.desk.generate_files
    });

    Route::name('pillar.')->prefix('pillars')->group(function () {
        Route::get('/', [PillarController::class, 'index'])->name('index'); // generator.pillar.index
        Route::get('/create', [PillarController::class, 'create'])->name('create'); // generator.pillar.create
        Route::post('/store', [PillarController::class, 'store'])->name('store'); // generator.pillar.store
        Route::get('/{pillar}', [PillarController::class, 'show'])->name('show'); // generator.pillar.show
        Route::get('/{pillar}/edit', [PillarController::class, 'edit'])->name('edit'); // generator.pillar.edit
        Route::patch('/{pillar}/update', [PillarController::class, 'update'])->name('update'); // generator.pillar.update
        Route::get('/{pillar}/destroy', [PillarController::class, 'destroy'])->name('destroy'); // generator.pillar.destroy
        Route::delete('/{pillar}/confirm-delete', [PillarController::class, 'confirm_destroy'])->name('confirm_destroy'); // generator.pillar.confirm_destroy
    });

    Route::name('pillar_type.')->prefix('pillar-types')->group(function () {
        Route::get('/', [PillarTypeController::class, 'index'])->name('index'); // generator.pillar_type.index
        Route::get('/create', [PillarTypeController::class, 'create'])->name('create'); // generator.pillar_type.create
        Route::post('/store', [PillarTypeController::class, 'store'])->name('store'); // generator.pillar_type.store
        Route::get('/{pillar_type}', [PillarTypeController::class, 'show'])->name('show'); // generator.pillar_type.show
        Route::get('/{pillar_type}/edit', [PillarTypeController::class, 'edit'])->name('edit'); // generator.pillar_type.edit
        Route::patch('/{pillar_type}/update', [PillarTypeController::class, 'update'])->name('update'); // generator.pillar_type.update
        Route::get('/{pillar_type}/destroy', [PillarTypeController::class, 'destroy'])->name('destroy'); // generator.pillar_type.destroy
        Route::delete('/{pillar_type}/confirm-delete', [PillarTypeController::class, 'confirm_destroy'])->name('confirm_destroy'); // generator.pillar_type.confirm_destroy
    });
});
