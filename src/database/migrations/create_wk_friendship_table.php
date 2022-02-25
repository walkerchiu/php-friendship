<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkFriendshipTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.friendship.friendships'), function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('user_id_a');
            $table->uuid('user_id_b');
            $table->string('state');
            $table->boolean('flag_a')->default(0);
            $table->boolean('flag_b')->default(0);

            $table->timestampsTz();

            $table->foreign('user_id_a')->references('id')
                  ->on(config('wk-core.table.user'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreign('user_id_b')->references('id')
                  ->on(config('wk-core.table.user'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index(['user_id_a', 'user_id_b', 'state']);
        });
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.friendship.friendships'));
    }
}
