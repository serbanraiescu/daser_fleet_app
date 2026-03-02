<?php

namespace FleetLog\App\Controllers;

require_once __DIR__ . '/../../core/qrcode_new.php';

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

        $size = (int)($_GET['sf'] ?? 4);

        // This library generates SVG without needing GD extension
        $qr = \QRCode::getMinimumQRCode($data, QR_ERROR_CORRECT_LEVEL_L);
        
        header('Content-Type: image/svg+xml');
        $qr->printSVG($size);
    }
}
