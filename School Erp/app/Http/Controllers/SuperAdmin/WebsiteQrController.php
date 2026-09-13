<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class WebsiteQrController extends Controller
{
    /**
     * Display the Website and Demo QR Generator module.
     */
    public function index(Request $request): View
    {
        $baseMarketingUrl = env('MAIN_MARKETING_URL', 'https://educorerp.com');
        
        // Clean trailing slash
        $baseMarketingUrl = rtrim($baseMarketingUrl, '/');
        
        // Target URLs
        $mainWebsiteUrl = $baseMarketingUrl;
        $demoPageUrl = $baseMarketingUrl . '/book-demo';
        
        // Direct QR code API image URLs with HIGH ERROR CORRECTION (ecc=H) & clear margins for 100% guaranteed camera scanning
        $mainQrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=350x350&ecc=H&margin=12&data=' . urlencode($mainWebsiteUrl);
        $demoQrImage = 'https://api.qrserver.com/v1/create-qr-code/?size=350x350&ecc=H&margin=12&data=' . urlencode($demoPageUrl);

        return view('superadmin.website-qr.index', compact(
            'baseMarketingUrl',
            'mainWebsiteUrl',
            'demoPageUrl',
            'mainQrImage',
            'demoQrImage'
        ));
    }
}
