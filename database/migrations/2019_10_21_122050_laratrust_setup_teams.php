<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class LaratrustSetupTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return  void
     */
    public function up()
    {
        // Create table for storing teams
        Schema::create('sau_teams', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::table('sau_role_user', function (Blueprint $table) {
            // Drop role foreign key and primary key
            $table->dropForeign(['role_id']);
            $table->dropPrimary(['user_id', 'role_id', 'user_type']);

            // Add team_id column
            $table->unsignedInteger('team_id')->nullable();

            // Create foreign keys
            $table->foreign('role_id')->references('id')->on('sau_roles')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('sau_teams')
                ->onUpdate('cascade')->onDelete('cascade');

            // Create a unique key
            $table->unique(['user_id', 'role_id', 'user_type', 'team_id']);
        });

        Schema::table('sau_permission_user', function (Blueprint $table) {
           // Drop permission foreign key and primary key
            $table->dropForeign(['permission_id']);
            $table->dropPrimary(['permission_id', 'user_id', 'user_type']);

            $table->foreign('permission_id')->references('id')->on('sau_permissions')
                ->onUpdate('cascade')->onDelete('cascade');

            // Add team_id column
            $table->unsignedInteger('team_id')->nullable();

            $table->foreign('team_id')->references('id')->on('sau_teams')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['user_id', 'permission_id', 'user_type', 'team_id'], 'permission_user_team_index_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return  void
     */
    public function down()
    {
    }
}
