<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTablesDangerousConditions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('sau_inspect_conditions_reports');
        Schema::dropIfExists('sau_inspect_conditions');
        Schema::dropIfExists('sau_inspect_conditions_types');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_inspect_conditions_reports');
        Schema::dropIfExists('sau_inspect_conditions');
        Schema::dropIfExists('sau_inspect_conditions_types');
    }
}
