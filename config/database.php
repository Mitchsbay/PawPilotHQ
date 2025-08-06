<?php
// Supabase configuration
define('SUPABASE_URL', 'https://lsmrudygghotkhxtqwmk.supabase.co');
define('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxzbXJ1ZHlnZ2hvdGtoeHRxd21rIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTM3ODg0NTcsImV4cCI6MjA2OTM2NDQ1N30.M8FzVYFhaMluh6uv2zz1NJD3lqNYKjiEuIPEp8aXfDs');
define('SUPABASE_SERVICE_KEY', 'your-service-role-key-here'); // Replace with actual service role key

class SupabaseClient {
    private $serviceKey;
    private $baseUrl;
    
    public function __construct() {
        $this->serviceKey = SUPABASE_SERVICE_KEY;
        $this->baseUrl = SUPABASE_URL;
    }
    
    public function makeRequest($endpoint, $method = 'GET', $data = null, $userToken = null) {
        $url = $this->baseUrl . '/rest/v1/' . $endpoint;
        
        $headers = [
            'apikey: ' . $this->serviceKey,
            'Authorization: Bearer ' . $this->serviceKey,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
        
        // If user token provided, also include it for RLS
        if ($userToken) {
            $headers[] = 'Authorization: Bearer ' . $userToken;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
    
    public function verifyToken($token) {
        $url = $this->baseUrl . '/auth/v1/user';
        
        $headers = [
            'apikey: ' . $this->serviceKey,
            'Authorization: Bearer ' . $token
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            return json_decode($response, true);
        }
        
        return false;
    }
}
?>