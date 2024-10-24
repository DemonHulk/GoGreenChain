<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NearService
{
    protected $client;
    protected $networkId;
    protected $nodeUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->networkId = env('NEAR_NETWORK', 'testnet');
        $this->nodeUrl = 'https://rpc.testnet.near.org'; // Endpoint para testnet
    }

    // Método para enviar NEAR tokens
    public function sendNear($fromAccountId, $privateKey, $toWalletAddress, $amount)
    {
        try {
            Log::info('Iniciando sendNear', [
                'fromAccountId' => $fromAccountId,
                'toWalletAddress' => $toWalletAddress,
                'amount' => $amount
            ]);

            // Convertir el monto a yoctoNEAR
            $amountInYocto = bcmul($amount, '1000000000000000000000000');

            // Obtener el nonce y block_hash
            $publicKey = $this->getPublicKeyFromPrivateKey($privateKey);
            Log::info('Clave pública obtenida', ['publicKey' => $publicKey]);

            $accessKeyInfo = $this->getAccessKey($fromAccountId, $publicKey);
            Log::info('AccessKey obtenido', $accessKeyInfo);

            $nonce = $accessKeyInfo['nonce'] + 1;
            $blockHash = $accessKeyInfo['block_hash'];

            // Construir la transacción
            $transaction = [
                'signer_id' => $fromAccountId,
                'public_key' => $publicKey,
                'nonce' => $nonce,
                'receiver_id' => $toWalletAddress,
                'block_hash' => $blockHash,
                'actions' => [
                    [
                        'Transfer' => [
                            'deposit' => $amountInYocto
                        ]
                    ]
                ],
            ];

            Log::info('Transacción construida', $transaction);

            // Serializar y firmar la transacción
            $serializedTx = $this->serializeAndSignTransaction($transaction, $privateKey);

            // Codificar en base64
            $base64Tx = base64_encode($serializedTx);
            Log::info('Transacción serializada y codificada', ['base64Tx' => $base64Tx]);

            // Enviar la transacción
            $response = $this->client->post($this->nodeUrl, [
                'json' => [
                    "jsonrpc" => "2.0",
                    "id" => "dontcare",
                    "method" => "broadcast_tx_commit",
                    "params" => [
                        $base64Tx
                    ]
                ]
            ]);

            $responseBody = $response->getBody()->getContents();
            $result = json_decode($responseBody, true);

            Log::info('Respuesta de NEAR:', [
                'statusCode' => $response->getStatusCode(),
                'result' => $result
            ]);

            if (isset($result['error'])) {
                Log::error('Error en la transacción:', ['error' => $result['error']]);
                $errorMessage = $result['error']['message'];
                if (isset($result['error']['data'])) {
                    $errorMessage .= ' - ' . json_encode($result['error']['data']);
                }
                return ['error' => $errorMessage];
            }

            // Transacción exitosa
            Log::info('Transacción exitosa', ['transaction' => $result['result']]);
            return ['success' => true, 'transaction' => $result['result']];

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                Log::error('Error en la solicitud:', [
                    'statusCode' => $e->getResponse()->getStatusCode(),
                    'responseBody' => $responseBody
                ]);
                return ['error' => 'Error en la solicitud: ' . $responseBody];
            } else {
                Log::error('Excepción en sendNear: ' . $e->getMessage());
                return ['error' => 'Error en la solicitud: ' . $e->getMessage()];
            }
        } catch (\Exception $e) {
            Log::error('Excepción en sendNear: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    private function getPrivateKeyBin($privateKey)
    {
        if (strpos($privateKey, 'ed25519:') === 0) {
            $privateKey = substr($privateKey, 8);
        }
        return $this->base58_decode($privateKey);
    }

    private function base58_decode($base58)
    {
        $chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $charsLen = strlen($chars);
        $decoded = gmp_init(0);
        $multi = gmp_init(1);

        for ($i = strlen($base58) - 1; $i >= 0; $i--) {
            $char = $base58[$i];
            $index = strpos($chars, $char);
            if ($index === false) {
                throw new \Exception('Caracter inválido en base58');
            }
            $decoded = gmp_add($decoded, gmp_mul($multi, $index));
            $multi = gmp_mul($multi, $charsLen);
        }

        $decodedBytes = gmp_export($decoded);
        if ($decodedBytes === false) {
            $decodedBytes = '';
        }

        // Añadir ceros iniciales
        $leadingZeros = 0;
        while ($leadingZeros < strlen($base58) && $base58[$leadingZeros] === '1') {
            $leadingZeros++;
        }
        return str_repeat("\x00", $leadingZeros) . $decodedBytes;
    }

    // Método para obtener el balance de una cuenta (opcional)
    public function getBalance($accountId)
    {
        try {
            $response = $this->client->post($this->nodeUrl, [
                'json' => [
                    "jsonrpc" => "2.0",
                    "id" => "dontcare",
                    "method" => "query",
                    "params" => [
                        "request_type" => "view_account",
                        "finality" => "final",
                        "account_id" => $accountId
                    ]
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['error'])) {
                throw new \Exception('Error en la consulta: ' . $body['error']['message']);
            }

            return $body['result']['amount']; // Retorna el balance en yoctoNEAR
        } catch (\Exception $e) {
            Log::error('Error al obtener el balance: ' . $e->getMessage());
            return null;
        }
    }

    private function getAccessKey($accountId, $publicKey)
    {
        $response = $this->client->post($this->nodeUrl, [
            'json' => [
                "jsonrpc" => "2.0",
                "id" => "dontcare",
                "method" => "query",
                "params" => [
                    "request_type" => "view_access_key",
                    "finality" => "final",
                    "account_id" => $accountId,
                    "public_key" => $publicKey,
                ]
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        if (isset($result['error'])) {
            throw new \Exception('Error en la consulta: ' . $result['error']['message']);
        }

        return $result['result'];
    }

    private function getPublicKeyFromPrivateKey($privateKey)
    {
        $privateKeyBin = $this->getPrivateKeyBin($privateKey);
        $publicKey = sodium_crypto_sign_publickey_from_secretkey($privateKeyBin);
        return 'ed25519:' . $this->base58_encode($publicKey);
    }

    private function base58_encode($data)
    {
        $chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $encoded = '';
        $number = gmp_import($data);
        $leadingZeros = 0;

        while (gmp_cmp($number, 0) > 0) {
            $remainder = gmp_mod($number, 58);
            $encoded = $chars[$remainder] . $encoded;
            $number = gmp_div_q($number, 58);
        }

        // Añadir ceros iniciales
        while ($leadingZeros < strlen($data) && $data[$leadingZeros] === "\x00") {
            $leadingZeros++;
        }

        return str_repeat('1', $leadingZeros) . $encoded;
    }

    private function serializeAndSignTransaction($transaction, $privateKey)
    {
        // Aquí implementarías la lógica para serializar y firmar la transacción
        // Este es un stub; implementa la lógica real según la documentación de NEAR
    }
}
