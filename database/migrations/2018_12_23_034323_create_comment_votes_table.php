<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comment_votes', function (Blueprint $table) {
            $table->increments('id');

            $table->uuid('comment_id');
            $table->foreign('comment_id')->references('id')->on('comments');
            $table->index('comment_id');

            $table->unsignedInteger('author_id');
            $table->foreign('author_id')->references('id')->on('users');
            $table->index('author_id');

            $table->boolean('value')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comment_votes');
    }
}
