<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('problem_id')->nullable();

            $table->string('language');
            $table->longText('code');
            $table->text('input')->nullable();
            $table->text('output')->nullable();
            $table->text('error')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->float('runtime')->nullable();
            $table->timestamps();

            // 🔍 Indexes for performance
            $table->index('user_id');              // Frequently queried by user
            $table->index('problem_id');           // For leaderboard, contest views
            $table->index('status');               // For admin/moderator queues
            $table->index(['user_id', 'problem_id']); // Composite index for analytics/queries

            // Optional: Index timestamps for cleanup, archiving
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submissions');
    }
}
