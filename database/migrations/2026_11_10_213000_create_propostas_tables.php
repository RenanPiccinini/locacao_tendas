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
        Schema::create('propostas', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 30);
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();

            // Dados da Proposta
            $table->date('data_proposta');
            $table->string('identificacao')->nullable();
            $table->string('status', 30)->default('rascunho');

            // Dados "De:" (Remetente / Locadora)
            $table->string('de_razao_social');
            $table->string('de_fantasia')->nullable();
            $table->string('de_cnpj', 30)->nullable();
            $table->string('de_ie', 30)->nullable();
            $table->string('de_im', 30)->nullable();
            $table->string('de_cep', 20)->nullable();
            $table->string('de_endereco')->nullable();
            $table->string('de_bairro', 100)->nullable();
            $table->string('de_cidade', 100)->nullable();
            $table->string('de_uf', 10)->nullable();
            $table->string('de_consultor')->nullable();
            $table->string('de_celular', 30)->nullable();
            $table->string('de_email')->nullable();

            // Dados "Para:" (Destinatário / Cliente)
            $table->string('para_razao_social');
            $table->string('para_fantasia')->nullable();
            $table->string('para_cnpj_cpf', 30)->nullable();
            $table->string('para_ie', 30)->nullable();
            $table->string('para_cep', 20)->nullable();
            $table->string('para_endereco')->nullable();
            $table->string('para_bairro', 100)->nullable();
            $table->string('para_cidade', 100)->nullable();
            $table->string('para_uf', 10)->nullable();
            $table->string('para_solicitante')->nullable();
            $table->string('para_celular', 30)->nullable();
            $table->string('para_email')->nullable();

            // Período(s)
            $table->date('data_saida')->nullable();
            $table->string('hora_saida', 10)->nullable();
            $table->date('data_inicio')->nullable();
            $table->string('hora_inicio', 10)->nullable();
            $table->date('data_fim')->nullable();
            $table->string('hora_fim', 10)->nullable();
            $table->date('data_retorno')->nullable();
            $table->string('hora_retorno', 10)->nullable();

            // Totais Financeiros
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->decimal('valor_desconto', 12, 2)->default(0);
            $table->decimal('valor_total_geral', 12, 2)->default(0);

            // Pagamento(s) e Observações
            $table->text('pagamentos')->nullable();
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });

        Schema::create('proposta_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposta_id')->constrained('propostas')->cascadeOnDelete();
            $table->foreignId('tenda_id')->nullable()->constrained('tendas')->nullOnDelete();
            $table->string('descricao');
            $table->unsignedInteger('quantidade')->default(1);
            $table->decimal('valor_periodo', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposta_itens');
        Schema::dropIfExists('propostas');
    }
};
