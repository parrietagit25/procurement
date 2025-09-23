<?php
require_once __DIR__ . '/../config/config.php';

class JWT {
    private static $secret = JWT_SECRET;
    private static $algorithm = JWT_ALGORITHM;

    // Generar token JWT
    public static function encode($payload) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);
        
        $payload['iat'] = time();
        $payload['exp'] = time() + JWT_EXPIRATION;
        $payload = json_encode($payload);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::$secret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }

    // Decodificar y verificar token JWT
    public static function decode($jwt) {
        $tokenParts = explode('.', $jwt);
        
        if(count($tokenParts) !== 3) {
            return false;
        }
        
        $header = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[0]));
        $payload = base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1]));
        $signatureProvided = $tokenParts[2];
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::$secret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        if($base64Signature !== $signatureProvided) {
            return false;
        }
        
        $payload = json_decode($payload, true);
        
        if(isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }

    // Verificar si el token es válido
    public static function validate($jwt) {
        $payload = self::decode($jwt);
        return $payload !== false;
    }

    // Obtener payload del token
    public static function getPayload($jwt) {
        return self::decode($jwt);
    }
}
?>