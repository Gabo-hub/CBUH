<?php
/**
 * Helper para estandarizar respuestas de la API
 */

class ApiResponse
{
    /**
     * Envía una respuesta exitosa
     */
    public static function success($data = null, $message = 'Operación exitosa', $code = 200)
    {
        self::send($code, true, $message, $data);
    }

    /**
     * Envía una respuesta de error
     */
    public static function error($message = 'Ha ocurrido un error', $code = 400, $data = null)
    {
        self::send($code, false, $message, $data);
    }

    /**
     * Envía la respuesta JSON y finaliza la ejecución
     */
    private static function send($code, $success, $message, $data)
    {
        header('Content-Type: application/json; charset=UTF-8');
        http_response_code($code);

        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}
