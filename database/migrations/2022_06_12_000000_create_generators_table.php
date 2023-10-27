<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('desks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('child_table')->nullable();
            $table->string('parent_table')->nullable();
            $table->string('directory')->nullable();
            $table->integer('columns_in_row')->default(2);
            $table->integer('highest_column_length')->default(30);

            $table->tinyInteger('generate_pages')->default(1)->comment('generate Pages');
            $table->tinyInteger('generate_model')->default(1)->comment('generate Model');
            $table->tinyInteger('generate_seeder')->default(0)->comment('generate Seeder');
            $table->tinyInteger('generate_cache')->default(1)->comment('generate Cache');
            $table->tinyInteger('generate_migration')->default(1)->comment('generate Migration');
            $table->tinyInteger('generate_resources')->default(1)->comment('generate Resources');
            $table->tinyInteger('generate_controller')->default(1)->comment('generate Controller');

            $table->tinyInteger('has_filter')->default(0)->comment('filterable column');
            $table->tinyInteger('has_opening')->default(0)->comment('opening column');
            $table->tinyInteger('has_polymorphic')->default(0)->comment('polymorphic table');
            $table->tinyInteger('has_remark')->default(0)->comment('remark column');
            $table->tinyInteger('has_description')->default(0)->comment('description column');
            $table->tinyInteger('has_soft_deletes')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pillar_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('faker')->nullable();
            $table->string('validate')->nullable()->comment('exists:___OBJECT_PLURAL___,id or date or string or boolean or numeric or image');
            $table->string('max_length')->nullable()->comment('max: 191 || 2024 in validation');
            $table->string('min_length')->nullable()->comment('min: 11 in validation');
            $table->string('full_length')->nullable()->comment('max: 16, 2 => 16 in migration');
            $table->string('float_length')->nullable()->comment('min: 16, 2 => 2 in migration');
            $table->string('mimes')->nullable()->comment('mimes:jpeg,png,jpg,gif,svg');
            $table->longText('guide')->nullable()->comment('type details');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pillars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('pillar_type_id')->nullable()->constrained();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('desk_pillars', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Column Title');
            $table->string('column');
            $table->string('column_length')->nullable();

            $table->string('default')->nullable();
            $table->tinyInteger('unique')->default(0)->comment("unique:___OBJECT_PLURAL___ or Rule::unique('___OBJECT_PLURAL___')->ignore(\$___OBJECT___)");
            $table->tinyInteger('requisite')->default(0)->comment('nullable or required');

            $table->string('empty_column')->default('none');
            $table->string('columns_in_row')->default(1);

            $table->string('indexing')->nullable()->comment('where to shown in index page');
            $table->string('filtering')->nullable()->comment('where to shown the position in index page');
            $table->integer('decorating')->nullable()->comment('position in create n edit page');

            $table->foreignId('desk_id')->constrained();
            $table->foreignId('table_id')->nullable()->constrained('desks');
            $table->foreignId('pillar_type_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('desks');
        Schema::dropIfExists('pillar_types');
        Schema::dropIfExists('pillars');
        Schema::dropIfExists('desk_pillars');
    }
};
