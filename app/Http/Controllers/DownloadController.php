<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\Entitlement;
use App\Models\ProductFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function __invoke(Request $request, Entitlement $entitlement, ProductFile $productFile)
    {
        abort_unless($entitlement->user_id === $request->user()->id, 403);
        abort_unless($productFile->product_id === $entitlement->product_id, 403);

        $disk = Storage::disk($productFile->disk);
        abort_unless($disk->exists($productFile->path), 404);

        Download::create([
            'entitlement_id' => $entitlement->id,
            'product_file_id' => $productFile->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'downloaded_at' => now(),
        ]);

        return $disk->download($productFile->path, $productFile->original_name);
    }
}
