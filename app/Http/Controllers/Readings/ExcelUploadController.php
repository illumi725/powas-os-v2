<?php

namespace App\Http\Controllers\Readings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExcelUploadController extends Controller
{
  public function upload(Request $request)
  {
    $request->validate([
      'file' => 'required|file|mimes:xlsx|max:10240',
    ]);

    $file = $request->file('file');
    $filename = Str::random(30) . '_' . time() . '.' . $file->getClientOriginalExtension();

    // Store in app storage (not /tmp) — InfinityFree blocks tmpfile()
    $path = $file->storeAs('excel-uploads', $filename, 'local');

    return response()->json(['path' => $path]);
  }
}
