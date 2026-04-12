<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class ReceiptMaintenanceController extends Controller
{
    public function __construct()
    {
        // Only allow authenticated users (role check handled by route middleware)
        $this->middleware('auth');
    }

    /**
     * Show the receipt maintenance dashboard
     */
    public function index()
    {
        return view('admin.receipt-maintenance');
    }

    /**
     * Run receipt analysis
     */
    public function analyze()
    {
        try {
            // Capture artisan command output
            Artisan::call('receipts:analyze');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => 'Error: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview receipt fixes (dry-run)
     */
    public function previewFix()
    {
        try {
            Artisan::call('receipts:fix', ['--dry-run' => true]);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'isDryRun' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => 'Error: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Apply receipt fixes
     */
    public function applyFix(Request $request)
    {
        // Require confirmation
        if (!$request->has('confirm') || $request->confirm !== 'yes') {
            return response()->json([
                'success' => false,
                'error' => 'Confirmation required'
            ], 400);
        }

        try {
            // Clear previous output
            \Artisan::call('cache:clear');
            
            // Run the command
            $exitCode = Artisan::call('receipts:fix');
            $output = Artisan::output();

            // Log for debugging
            \Log::info('Receipt fix command executed', [
                'exit_code' => $exitCode,
                'output_length' => strlen($output)
            ]);

            return response()->json([
                'success' => true,
                'output' => $output ?: 'Command completed with no output (exit code: ' . $exitCode . ')',
                'message' => 'Receipt fixes applied successfully!',
                'exit_code' => $exitCode
            ]);
        } catch (\Exception $e) {
            \Log::error('Receipt fix command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'output' => 'Error: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
