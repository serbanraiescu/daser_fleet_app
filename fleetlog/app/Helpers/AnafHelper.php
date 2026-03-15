<?php

namespace FleetLog\App\Helpers;

/**
 * ANAF DATA FETCHING HELPER
 */
class AnafHelper
{
    /**
     * Fetch data from ANAF API for a given CUI.
     * 
     * @param string $cui The VAT ID / CUI
     * @return array Normalized response
     */
    public static function fetchData(string $cui): array
    {
        // 1. Normalize Input (Remove 'RO' and spaces)
        $cui = preg_replace('/[^0-9]/', '', $cui);

        if (empty($cui)) {
            return ['success' => false, 'error' => 'Invalid CUI format'];
        }

        // 2. Define API Endpoints (v9 is primary, v8 is backup)
        $endpoints = [
            "https://webservicesp.anaf.ro/api/PlatitorTvaRest/v9/tva",
            "https://webservicesp.anaf.ro/PlatitorTvaRest/api/v8/ws/tva"
        ];

        $response = null;
        $lastError = "";

        // 3. Try endpoints sequentially
        foreach ($endpoints as $url) {
            $requestData = json_encode([
                ['cui' => $cui, 'data' => date('Y-m-d')]
            ]);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'User-Agent: DaserApp/1.0'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            
            // Optional: Disable SSL check if having certificate issues
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                $lastError = curl_error($ch);
            }
            curl_close($ch);

            if ($httpCode === 200 && $result) {
                $response = $result;
                break;
            }
        }

        if (!$response) {
            return ['success' => false, 'error' => "ANAF API Connection Failed. Error: $lastError"];
        }

        // 4. Parse & Normalize Data
        $decoded = json_decode($response, true);
        
        if (empty($decoded['found'])) {
            return ['success' => false, 'error' => 'CUI not found in ANAF database'];
        }

        $raw = $decoded['found'][0];
        
        // Extracting normalized data exactly as user requested:
        // CUI, DENUMIRE, REG COM, JUDET, LOCALITATE, ADRESA
        return [
            'success'       => true,
            'cui'           => $cui,
            'company_name'  => $raw['date_generale']['denumire'] ?? '',
            'address'       => $raw['date_generale']['adresa'] ?? '',
            'reg_com'       => $raw['date_generale']['nrRegCom'] ?? '',
            'county'        => $raw['adresa_sediu_social']['scod_JudetAuto'] ?? '',
            'city'          => $raw['adresa_sediu_social']['slocalitate'] ?? '',
            // Additional info if needed
            'vat_payer'     => (isset($raw['inregistrare_scop_Tva']['scpTVA']) && $raw['inregistrare_scop_Tva']['scpTVA'] === true),
        ];
    }
}
