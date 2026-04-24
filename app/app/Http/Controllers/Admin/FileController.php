<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function serve(string $path)
    {
        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        $file     = Storage::disk('private')->get($path);
        $mimeType = Storage::disk('private')->mimeType($path);

        return response($file, 200)->header('Content-Type', $mimeType);
    }
}
