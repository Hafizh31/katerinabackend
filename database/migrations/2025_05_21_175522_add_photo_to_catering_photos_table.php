<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('catering_photos', function (Blueprint $table) {
            $table->string('photo')->after('id'); // atau after kolom lain sesuai struktur kamu
        });
    }

    public function down(): void
    {
        Schema::table('catering_photos', function (Blueprint $table) {
            $table->dropColumn('photo');
        });
    }
};
