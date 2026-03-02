<?php

namespace FleetLog\App\Controllers;

require_once __DIR__ . '/../../core/qrcode.php';

class QrController extends BaseController
{
    public function generate(): void
    {
        $data = $_GET['d'] ?? '';
        if (empty($data)) {
            header('HTTP/1.1 400 Bad Request');
            echo 'Missing data parameter';
            return;
        }

        // Options: 'sf' => scale factor (size)
        $options = [
            'sf' => (float)($_GET['sf'] ?? 4),
            'p' => (int)($_GET['p'] ?? 2) // padding
        ];

        $generator = new \QRCode($data, $options);
        $generator->output_image();
    }
}
