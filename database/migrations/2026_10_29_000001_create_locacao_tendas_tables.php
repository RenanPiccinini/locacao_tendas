<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Clientes
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone');
            $table->string('email')->nullable();
            $table->string('cpf_cnpj')->nullable();
            $table->string('endereco');
            $table->string('cidade')->nullable();
            $table->string('cep')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        // 2. Tendas (Estoque)
        Schema::create('tendas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('tamanho'); // Ex: 3x3, 5x5, 10x10
            $table->text('descricao')->nullable();
            $table->integer('quantidade_total')->default(1);
            $table->integer('quantidade_disponivel')->default(1);
            $table->decimal('valor_diaria', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Pedidos
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('montador_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('data_instalacao');
            $table->time('hora_instalacao')->nullable();
            $table->date('data_retirada');
            $table->time('hora_retirada')->nullable();
            $table->string('endereco_instalacao');
            $table->string('cidade_instalacao')->nullable();
            $table->string('cep_instalacao')->nullable();
            $table->enum('status', [
                'pendente',
                'os_gerada',
                'em_instalacao',
                'instalado',
                'aguardando_retirada',
                'em_retirada',
                'concluido',
                'cancelado'
            ])->default('pendente');
            $table->decimal('valor_total', 10, 2)->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        // 4. Pivot Pedido - Tendas
        Schema::create('pedido_tendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('tenda_id')->constrained('tendas')->onDelete('cascade');
            $table->integer('quantidade')->default(1);
            $table->timestamps();
        });

        // 5. Ordens de Serviço (OS de Instalação e Retirada)
        Schema::create('ordens_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->enum('tipo', ['instalacao', 'retirada']);
            $table->string('numero_os')->unique();
            $table->enum('status', ['pendente', 'em_andamento', 'concluida', 'cancelada'])->default('pendente');
            $table->json('fotos')->nullable();
            $table->text('observacoes_montador')->nullable();
            $table->timestamp('iniciado_em')->nullable();
            $table->timestamp('concluido_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordens_servico');
        Schema::dropIfExists('pedido_tendas');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('tendas');
        Schema::dropIfExists('clientes');
    }
};

