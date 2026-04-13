<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('settings.settings');
    }

    public function downloadBackup($fileName)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $filePath = storage_path('app/backups/' . $fileName);
        
        if (File::exists($filePath)) {
            return response()->download($filePath);
        }
        
        abort(404, 'Backup file not found.');
    }
}
