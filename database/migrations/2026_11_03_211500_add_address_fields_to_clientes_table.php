<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'numero')) {
                $table->string('numero', 20)->nullable()->after('endereco');
            }
            if (!Schema::hasColumn('clientes', 'complemento')) {
                $table->string('complemento', 100)->nullable()->after('numero');
            }
            if (!Schema::hasColumn('clientes', 'bairro')) {
                $table->string('bairro', 100)->nullable()->after('complemento');
            }
            if (!Schema::hasColumn('clientes', 'estado')) {
                $table->string('estado', 10)->nullable()->after('cidade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['numero', 'complemento', 'bairro', 'estado']);
        });
    }
};
