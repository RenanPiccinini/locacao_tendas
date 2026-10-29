<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $instance;

    public function __construct()
    {
        $this->baseUrl = config('services.evolution.url');
        $this->apiKey = config('services.evolution.key');
        $this->instance = config('services.evolution.instance');
    }

    /**
     * Envia mensagem de texto para número de WhatsApp via Evolution API
     */
    public function enviarMensagem(string $numero, string $mensagem): bool
    {
        try {
            // Limpa caracteres especiais do telefone
            $numeroLimpo = preg_replace('/\D/', '', $numero);
            if (empty($numeroLimpo)) {
                return false;
            }

            // Garante DDI 55 do Brasil se tiver tamanho típico (ex: 51999999999 -> 11 dígitos)
            if (strlen($numeroLimpo) === 10 || strlen($numeroLimpo) === 11) {
                $numeroLimpo = '55' . $numeroLimpo;
            }

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

            Log::warning("Evolution API retornou status {$response->status()}: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Erro ao enviar mensagem via Evolution API: " . $e->getMessage());
            return false;
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

            return ['instance' => ['state' => 'disconnected'], 'message' => $response->body()];
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
            ])->timeout(10)->get($url);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Falha ao buscar QR Code', 'details' => $response->body()];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Logout / Desconectar instância
     */
    public function desconectar(): bool
    {
        try {
            $url = "{$this->baseUrl}/instance/logout/{$this->instance}";
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->timeout(10)->delete($url);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Erro ao desconectar instância: " . $e->getMessage());
            return false;
        }
    }
}

