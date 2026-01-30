<?php
/**
 * Clase para interactuar con Supabase Storage via REST API
 */
class SupabaseStorage
{
    private $url;
    private $key;
    private $bucket;

    public function __construct()
    {
        $this->url = getenv('SUPABASE_URL');
        $this->key = getenv('SUPABASE_ANON_KEY');
        $this->bucket = 'cbuh_files'; // Ajustar según el nombre del bucket en Supabase
    }

    /**
     * Sube un archivo a un bucket de Supabase
     */
    public function upload($path, $file_tmp, $content_type)
    {
        $ch = curl_init();

        $uploadUrl = "{$this->url}/storage/v1/object/{$this->bucket}/{$path}";
        $fileContent = file_get_contents($file_tmp);

        curl_setopt($ch, CURLOPT_URL, $uploadUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->key}",
            "apikey: {$this->key}",
            "Content-Type: {$content_type}",
            "x-upsert: true"
        ]);

        // Verificación SSL condicional según el entorno
        $isProduction = getenv('APP_ENV') === 'production';
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $isProduction);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $isProduction ? 2 : 0);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if (function_exists('logDebug')) {
            logDebug("SUPABASE UPLOAD: Path=$path | HTTP=$httpCode" . ($curlError ? " | Error=$curlError" : ""));
            if ($httpCode >= 400) {
                logDebug("SUPABASE ERROR RESPONSE: " . $response);
            }
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            // Retorna la URL pública aproximada (asumiendo bucket público)
            return "{$this->url}/storage/v1/object/public/{$this->bucket}/{$path}";
        }

        return false;
    }
}
