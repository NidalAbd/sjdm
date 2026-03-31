<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            $table->string('native_name', 100);
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10);
            $table->string('group', 30)->default('app');
            $table->string('key', 150);
            $table->text('value');
            $table->timestamps();

            $table->unique(['locale', 'group', 'key'], 'translations_unique');
            $table->index(['locale', 'group']);

            $table->foreign('locale')
                  ->references('code')
                  ->on('languages')
                  ->onDelete('cascade');
        });

        // Seed the 17 default languages
        DB::table('languages')->insert([
            ['code' => 'en', 'name' => 'English', 'native_name' => 'English', 'direction' => 'ltr', 'is_active' => true, 'is_default' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ar', 'name' => 'Arabic', 'native_name' => 'العربية', 'direction' => 'rtl', 'is_active' => true, 'is_default' => false, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'fr', 'name' => 'French', 'native_name' => 'Français', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pt', 'name' => 'Portuguese', 'native_name' => 'Português', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'zh', 'name' => 'Chinese', 'native_name' => '中文', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ja', 'name' => 'Japanese', 'native_name' => '日本語', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ko', 'name' => 'Korean', 'native_name' => '한국어', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'hi', 'name' => 'Hindi', 'native_name' => 'हिन्दी', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'tr', 'name' => 'Turkish', 'native_name' => 'Türkçe', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'it', 'name' => 'Italian', 'native_name' => 'Italiano', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pl', 'name' => 'Polish', 'native_name' => 'Polski', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 14, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'nl', 'name' => 'Dutch', 'native_name' => 'Nederlands', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 15, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'vi', 'name' => 'Vietnamese', 'native_name' => 'Tiếng Việt', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 16, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'th', 'name' => 'Thai', 'native_name' => 'ไทย', 'direction' => 'ltr', 'is_active' => true, 'is_default' => false, 'sort_order' => 17, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
        Schema::dropIfExists('languages');
    }
};
