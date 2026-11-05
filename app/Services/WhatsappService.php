<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $instance;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.evolution.url'), '/');
        $this->apiKey = config('services.evolution.key');
        $this->instance = config('services.evolution.instance');
    }

    /**
     * Envia mensagem de texto para número de WhatsApp via Evolution API
     */
    public function enviarMensagem(string $numero, string $mensagem): bool
    {
        try {
            $numeroLimpo = preg_replace('/\D/', '', $numero);
            if (empty($numeroLimpo)) {
                return false;
            }

            if (strlen($numeroLimpo) === 10 || strlen($numeroLimpo) === 11) {
                $numeroLimpo = '55' . $numeroLimpo;
            }

            // Mecanismo anti-duplicidade: bloqueia reenvio da mesmíssima mensagem para o mesmo número em curto intervalo (60 segundos)
            $dedupKey = 'wa_msg_dedup_' . md5($numeroLimpo . ':' . trim($mensagem));
            if (Cache::has($dedupKey)) {
                Log::warning("Envio repetido de mensagem idêntica para {$numeroLimpo} bloqueado pelo sistema anti-duplicidade.");
                return true;
            }
            Cache::put($dedupKey, true, now()->addSeconds(60));

            $url = "{$this->baseUrl}/message/sendText/{$this->instance}";

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($url, [
                'number' => $numeroLimpo,
                'text' => $mensagem,
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp enviado com sucesso para {$numeroLimpo}");
                return true;
            }

            // Se a requisição falhou, limpa o lock para permitir retry
            Cache::forget($dedupKey);

            Log::warning("Evolution API retornou status {$response->status()}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            if (isset($dedupKey)) {
                Cache::forget($dedupKey);
            }
            Log::error("Erro ao enviar mensagem via Evolution API: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria a instância caso ela não exista ainda no servidor
     */
    public function criarInstanciaSeNaoExistir(): array
    {
        try {
            $url = "{$this->baseUrl}/instance/create";
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(15)->post($url, [
                'instanceName' => $this->instance,
                'integration' => 'WHATSAPP-BAILEYS',
                'qrcode' => true,
            ]);

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error("Erro ao criar instancia Evolution: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Obtém estado da conexão da instância (open, connecting, close)
     */
    public function getStatus(): array
    {
        try {
            $url = "{$this->baseUrl}/instance/connectionState/{$this->instance}";
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->timeout(8)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            // Se a instância não existe ainda (404), tentamos criá-la automaticamente
            if ($response->status() === 404) {
                $this->criarInstanciaSeNaoExistir();
            }

            return ['instance' => ['state' => 'close'], 'message' => $response->body()];
        } catch (\Exception $e) {
            return ['instance' => ['state' => 'error'], 'error' => $e->getMessage()];
        }
    }

    /**
     * Solicita QR Code para conectar a instância
     */
    public function getQrCode(): array
    {
        try {
            $url = "{$this->baseUrl}/instance/connect/{$this->instance}";
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->timeout(15)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'base64' => $this->extrairBase64($data),
                    'raw' => $data,
                ];
            }

            // Se a instância não existia ainda (404), cria e busca novamente
            if ($response->status() === 404) {
                $criada = $this->criarInstanciaSeNaoExistir();
                $base64Criada = $this->extrairBase64($criada);

                if ($base64Criada) {
                    return [
                        'success' => true,
                        'base64' => $base64Criada,
                        'raw' => $criada,
                    ];
                }

                // Tenta chamar o connect mais uma vez
                $retry = Http::withHeaders(['apikey' => $this->apiKey])
                    ->timeout(15)
                    ->get($url);

                if ($retry->successful()) {
                    $retryData = $retry->json();
                    return [
                        'success' => true,
                        'base64' => $this->extrairBase64($retryData),
                        'raw' => $retryData,
                    ];
                }
            }

            return ['error' => 'Falha ao buscar QR Code', 'details' => $response->body()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Extrai o base64 do QR Code dos diferentes formatos da Evolution API
     */
    protected function extrairBase64($response): ?string
    {
        if (!is_array($response)) {
            return null;
        }

        if (!empty($response['base64']) && is_string($response['base64'])) {
            return $response['base64'];
        }

        if (!empty($response['qrcode']['base64']) && is_string($response['qrcode']['base64'])) {
            return $response['qrcode']['base64'];
        }

        if (!empty($response['instance']['qrcode']['base64']) && is_string($response['instance']['qrcode']['base64'])) {
            return $response['instance']['qrcode']['base64'];
        }

        if (!empty($response['qrcode']) && is_string($response['qrcode']) && str_starts_with($response['qrcode'], 'data:image')) {
            return $response['qrcode'];
        }

        if (!empty($response['code']) && is_string($response['code']) && str_starts_with($response['code'], 'data:image')) {
            return $response['code'];
        }

        return null;
    }

    /**
     * Logout e Deleta a instância respectiva da Evolution API.
     * CUIDADO ESTRITO: Deleta APENAS a instância desta aplicação ($this->instance).
     */
    public function desconectar(): bool
    {
        $nomeInstancia = trim($this->instance);

        if (empty($nomeInstancia)) {
            Log::warning("Tentativa de desconectar/deletar instância WhatsApp sem nome configurado.");
            return false;
        }

        try {
            Log::info("Iniciando exclusão exclusiva da instância respectiva [{$nomeInstancia}] na Evolution API");

            // 1. Tenta logout prévio para encerrar o socket do Baileys
            try {
                $urlLogout = "{$this->baseUrl}/instance/logout/{$nomeInstancia}";
                Http::withHeaders([
                    'apikey' => $this->apiKey,
                ])->timeout(5)->delete($urlLogout);
            } catch (\Exception $eLogout) {
                Log::info("Aviso logout prévio instância [{$nomeInstancia}]: " . $eLogout->getMessage());
            }

            // 2. Deleta estritamente a instância respectiva na Evolution API
            $urlDelete = "{$this->baseUrl}/instance/delete/{$nomeInstancia}";
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->timeout(12)->delete($urlDelete);

            $status = $response->status();
            Log::info("Resultado da exclusão da instância [{$nomeInstancia}]: status {$status}");

            // 200, 201 ou 404 (já deletada anteriormente) representam sucesso na remoção
            return $response->successful() || $status === 404;
        } catch (\Exception $e) {
            Log::error("Erro ao deletar instância [{$nomeInstancia}] da Evolution API: " . $e->getMessage());
            return false;
        }
    }
}
