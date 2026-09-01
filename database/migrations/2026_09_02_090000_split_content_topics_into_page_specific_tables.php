<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES_BY_PAGE = [
        'sakramenty' => 'sakramenty_topics',
        'parafia' => 'parafia_topics',
        'liturgia' => 'liturgia_topics',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::TABLES_BY_PAGE as $table) {
            Schema::create($table, function (Blueprint $table) {
                $table->id();
                $table->string('icon_url')->nullable();
                $table->string('title');
                $table->longText('content')->default('');
                $table->timestamp('visible_from')->nullable();
                $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
                $table->unsignedInteger('order')->default(0);
                $table->timestamps();

                $table->index(['order']);
            });
        }

        if (Schema::hasTable('content_topics')) {
            foreach (self::TABLES_BY_PAGE as $page => $table) {
                $rows = DB::table('content_topics')->where('page', $page)->get();

                foreach ($rows as $row) {
                    DB::table($table)->insert([
                        'id' => $row->id,
                        'icon_url' => $row->icon_url,
                        'title' => $row->title,
                        'content' => $row->content,
                        'visible_from' => $row->visible_from,
                        'author_id' => $row->author_id,
                        'order' => $row->order,
                        'created_at' => $row->created_at,
                        'updated_at' => $row->updated_at,
                    ]);
                }
            }

            Schema::dropIfExists('content_topics');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLES_BY_PAGE as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('content_topics', function (Blueprint $table) {
            $table->id();
            $table->string('page');
            $table->string('icon_url')->nullable();
            $table->string('title');
            $table->longText('content')->default('');
            $table->timestamp('visible_from')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->index(['page', 'order']);
        });
    }
};
