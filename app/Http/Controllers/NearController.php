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
                throw new \Exception('Error al obtener el saldo: ' . $body['error']['message']);
            }

            return $body['result']['amount'] ?? 0;
        } catch (\Exception $e) {
            Log::error('Error en getBalance: ' . $e->getMessage());
            return 0;
        }
    }

    // Función para obtener el access key
    private function getAccessKey($accountId, $publicKey)
    {
        try {
            $response = $this->client->post($this->nodeUrl, [
                'json' => [
                    "jsonrpc" => "2.0",
                    "id" => "dontcare",
                    "method" => "query",
                    "params" => [
                        "request_type" => "view_access_key",
                        "finality" => "final",
                        "account_id" => $accountId,
                        "public_key" => $publicKey
                    ]
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['error'])) {
                throw new \Exception('Error obteniendo el access key: ' . $body['error']['message']);
            }

            if (!isset($body['result'])) {
                throw new \Exception('No se encontró el access key para la clave pública proporcionada.');
            }

            return $body['result'];
        } catch (\Exception $e) {
            Log::error('Error en getAccessKey: ' . $e->getMessage());
            throw $e;
        }
    }

    // Función para obtener la clave pública a partir de la clave privada
    private function getPublicKeyFromPrivateKey($privateKey)
    {
        if (strpos($privateKey, 'ed25519:') === 0) {
            $privateKey = substr($privateKey, 8);
        }
        // NEAR utiliza claves privadas en base58
        $privateKeyBin = $this->base_decode($privateKey);

        $keyLength = strlen($privateKeyBin);

        if ($keyLength === 64) {
            // Clave privada extendida (64 bytes)
            $publicKeyBin = substr($privateKeyBin, 32, 32);
        } elseif ($keyLength === 32) {
            // Semilla de 32 bytes
            $keyPair = \sodium_crypto_sign_seed_keypair($privateKeyBin);
            $publicKeyBin = \sodium_crypto_sign_publickey($keyPair);
        } else {
            throw new \Exception('Tamaño de clave privada inválido.');
        }

        $publicKey = 'ed25519:' . $this->base_encode($publicKeyBin);

        return $publicKey;
    }

    // Función para serializar y firmar la transacción
    private function serializeAndSignTransaction($transaction, $privateKey)
    {
        // Serializar la transacción
        $serializedTx = $this->serializeTransaction($transaction);
    
        // Decodificar la clave privada
        if (strpos($privateKey, 'ed25519:') === 0) {
            $privateKey = substr($privateKey, 8);
        }
        $privateKeyBin = $this->base_decode($privateKey);
    
        // Firmar la transacción
        $signature = \sodium_crypto_sign_detached($serializedTx, $privateKeyBin);
    
        // Tipo de clave para la firma (0 para ED25519)
        $signatureType = chr(0);
    
        // Serializar la firma
        $serializedSignature = $signatureType . $signature;
    
        // Crear el SignedTransaction: firma serializada + transacción serializada
        $signedTx = $serializedSignature . $serializedTx;
    
        return $signedTx;
    }
    

    // Función para serializar la transacción
    private function serializeTransaction($transaction)
    {
        $buffer = '';
    
        // Serializar signer_id
        $signerIdSerialized = $this->serializeString($transaction['signer_id']);
        Log::info('signer_id serializado (hex): ' . bin2hex($signerIdSerialized));
        $buffer .= $signerIdSerialized;
    
        // Serializar public_key
        $publicKeySerialized = $this->serializePublicKey($transaction['public_key']);
        Log::info('public_key serializado (hex): ' . bin2hex($publicKeySerialized));
        $buffer .= $publicKeySerialized;
    
        // Serializar nonce (u64)
        $nonceSerialized = $this->serializeU64($transaction['nonce']);
        Log::info('nonce serializado (hex): ' . bin2hex($nonceSerialized));
        $buffer .= $nonceSerialized;
    
        // Serializar receiver_id
        $receiverIdSerialized = $this->serializeString($transaction['receiver_id']);
        Log::info('receiver_id serializado (hex): ' . bin2hex($receiverIdSerialized));
        $buffer .= $receiverIdSerialized;
    
        // Serializar block_hash (32 bytes)
        $blockHashSerialized = $this->base_decode($transaction['block_hash']);
        Log::info('block_hash serializado (hex): ' . bin2hex($blockHashSerialized));
        $buffer .= $blockHashSerialized;
    
        // Serializar número de acciones (u32)
        $actionsCount = count($transaction['actions']);
        $actionsCountSerialized = $this->serializeU32($actionsCount);
        Log::info('actions_count serializado (hex): ' . bin2hex($actionsCountSerialized));
        $buffer .= $actionsCountSerialized;
    
        // Serializar cada acción
        foreach ($transaction['actions'] as $index => $action) {
            $actionSerialized = $this->serializeAction($action);
            Log::info("Acción #{$index} serializada (hex): " . bin2hex($actionSerialized));
            $buffer .= $actionSerialized;
        }
    
        Log::info('Transacción completa serializada (hex): ' . bin2hex($buffer));
        return $buffer;
    }
    
    

    // Función para serializar la transacción firmada
    private function serializeSignedTransaction($transaction, $signature)
    {
        $buffer = '';

        // Serializar la firma
        $buffer .= $this->serializeSignature($signature);

        // Serializar la transacción
        $buffer .= $this->serializeTransaction($transaction);

        return $buffer;
    }

    // Función para serializar una firma
    private function serializeSignature($signature)
    {
        // Tipo de clave (0) para ED25519
        $keyType = chr(0);

        return $keyType . $signature;
    }

    // Función para serializar una acción
    private function serializeAction($action)
    {
        $buffer = '';

        if (isset($action['Transfer'])) {
            // Tipo de acción Transfer (3)
            $buffer .= chr(3);
            $buffer .= $this->serializeU128($action['Transfer']['deposit']);
        }
        // Agregar otros tipos de acciones si es necesario

        return $buffer;
    }

    // Funciones auxiliares de serialización

    private function serializeString($string)
    {
        $length = $this->encodeVariantLength(strlen($string));
        return $length . $string;
    }

    private function serializePublicKey($publicKey)
    {
        // Tipo de clave (0) para ED25519
        $keyType = chr(0);
    
        // Decodificar la clave pública de base58 a binario
        if (strpos($publicKey, 'ed25519:') === 0) {
            $publicKey = substr($publicKey, 8);
        }
        $keyData = $this->base_decode($publicKey);
    
        // Verificar longitud de la clave pública
        if (strlen($keyData) !== 32) {
            throw new \Exception('La clave pública debe tener 32 bytes.');
        }
    
        return $keyType . $keyData;
    }
    

    private function serializeU64($value)
    {
        $value = gmp_init($value);
        $result = '';
        for ($i = 0; $i < 8; $i++) {
            $byte = gmp_intval(gmp_and($value, 0xFF));
            $result .= chr($byte);
            $value = gmp_div_q($value, 256);
        }
        return $result;
    }
    

    private function serializeU32($value)
    {
        $value = gmp_init($value);
        $result = '';
        for ($i = 0; $i < 4; $i++) {
            $byte = gmp_intval(gmp_and($value, 0xFF));
            $result .= chr($byte);
            $value = gmp_div_q($value, 256);
        }
        return $result;
    }
    

    private function serializeU128($value)
    {
        $value = gmp_init($value);
        $result = '';
        for ($i = 0; $i < 16; $i++) {
            $byte = gmp_intval(gmp_and($value, 0xFF));
            $result .= chr($byte);
            $value = gmp_div_q($value, 256);
        }
        return $result;
    }
    

    // Función para codificar la longitud variante
    private function encodeVariantLength($value)
    {
        $value = gmp_init($value);
        $result = '';
        do {
            $byte = gmp_intval(gmp_and($value, 0x7F));
            $value = gmp_div_q($value, 128);
            if (gmp_cmp($value, 0) > 0) {
                $byte |= 0x80;
            }
            $result .= chr($byte);
        } while (gmp_cmp($value, 0) > 0);
        return $result;
    }
    

    // Implementación de base58 encoding
    private function base_encode($input)
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base = strlen($alphabet);
        $num = gmp_import($input, 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);

        $encoded = '';
        while (gmp_cmp($num, 0) > 0) {
            list($num, $rem) = gmp_div_qr($num, $base);
            $encoded = $alphabet[gmp_intval($rem)] . $encoded;
        }

        // Añadir '1's para ceros iniciales
        $pad = '';
        $inputBytes = unpack('C*', $input);
        foreach ($inputBytes as $byte) {
            if ($byte === 0) {
                $pad .= '1';
            } else {
                break;
            }
        }

        return $pad . $encoded;
    }

    // Implementación de base58 decoding
    private function base_decode($input)
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $base = strlen($alphabet);
        $decoded = gmp_init(0);
        
        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];
            $index = strpos($alphabet, $char);
            if ($index === false) {
                throw new \Exception('Caracter inválido en base58');
            }
            $decoded = gmp_mul($decoded, $base);
            $decoded = gmp_add($decoded, $index);
        }
        
        $decodedBytes = gmp_export($decoded);
        if ($decodedBytes === false) {
            $decodedBytes = '';
        }
        
        // Añadir ceros iniciales
        $numLeadingZeros = 0;
        while ($numLeadingZeros < strlen($input) && $input[$numLeadingZeros] === '1') {
            $numLeadingZeros++;
        }
        
        return str_repeat("\x00", $numLeadingZeros) . $decodedBytes;
    }
    
    
}