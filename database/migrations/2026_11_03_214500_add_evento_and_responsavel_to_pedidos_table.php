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
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'data_evento')) {
                $table->date('data_evento')->nullable()->after('hora_instalacao');
            }
            if (!Schema::hasColumn('pedidos', 'nome_responsavel')) {
                $table->string('nome_responsavel', 255)->nullable()->after('observacoes');
            }
            if (!Schema::hasColumn('pedidos', 'telefone_responsavel')) {
                $table->string('telefone_responsavel', 30)->nullable()->after('nome_responsavel');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['data_evento', 'nome_responsavel', 'telefone_responsavel']);
        });
    }
};
