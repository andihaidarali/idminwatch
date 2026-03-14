<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_pages', function (Blueprint $table): void {
            $table->string('title_en')->nullable()->after('content');
            $table->longText('content_en')->nullable()->after('title_en');
        });
    }

    public function down(): void
    {
        Schema::table('about_pages', function (Blueprint $table): void {
            $table->dropColumn(['title_en', 'content_en']);
        });
    }
};
