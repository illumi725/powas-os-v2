<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Ifsnop\Mysqldump\Mysqldump;

class DatabaseBackup extends Component
{
    public $backups = [];
    public $showingConfirmRestoreModal = false;
    public $showingConfirmDeleteModal = false;
    public $selectedBackup = null;

    public function mount()
    {
        $this->loadBackups();
    }

    public function loadBackups()
    {
        $this->backups = [];
        
        $path = storage_path('app/backups');
        
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $files = File::files($path);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $this->backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1048576, 2) . ' MB',
                    'date' => date('Y-m-d H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                ];
            }
        }
        
        // Sort by newest first
        usort($this->backups, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
    }

    public function createBackup()
    {
        if (!auth()->user()->hasRole('admin')) {
            $this->dispatch('backupMessage', [
                'message' => 'Unauthorized action!',
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
            return;
        }

        try {
            $path = storage_path('app/backups');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $fileName = 'powas_backup_' . date('Y_m_d_H_i_s') . '.sql';
            $filePath = $path . '/' . $fileName;

            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            $dumpSettings = ['add-drop-table' => true];
            $dump = new Mysqldump("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass, $dumpSettings);
            $dump->start($filePath);

            $this->loadBackups();
            $this->dispatch('backupMessage', [
                'message' => 'Database backup created successfully!',
                'messageType' => 'success',
                'position' => 'top-right'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('backupMessage', [
                'message' => 'Backup failed: ' . $e->getMessage(),
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
        }
    }

    public function downloadBackup($fileName)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $filePath = storage_path('app/backups/' . $fileName);
        
        if (File::exists($filePath)) {
            return response()->download($filePath);
        }
        
        $this->dispatch('backupMessage', [
            'message' => 'Backup file not found!',
            'messageType' => 'error',
            'position' => 'top-right'
        ]);
    }

    public function confirmRestore($fileName)
    {
        if (!auth()->user()->hasRole('admin')) {
            $this->dispatch('backupMessage', [
                'message' => 'Unauthorized action!',
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
            return;
        }

        $this->selectedBackup = $fileName;
        $this->showingConfirmRestoreModal = true;
    }

    public function restoreBackup()
    {
        if (!auth()->user()->hasRole('admin')) {
            $this->showingConfirmRestoreModal = false;
            $this->dispatch('backupMessage', [
                'message' => 'Unauthorized action!',
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
            return;
        }

        try {
            $filePath = storage_path('app/backups/' . $this->selectedBackup);
            
            if (!File::exists($filePath)) {
                throw new \Exception('Backup file not found!');
            }

            $dbHost = config('database.connections.mysql.host');
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');

            // Wipe database before restoring because older backups lack DROP TABLE commands
            Artisan::call('db:wipe', ['--force' => true]);

            $dump = new Mysqldump("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPass);
            $dump->restore($filePath);

            $this->showingConfirmRestoreModal = false;
            $this->selectedBackup = null;
            $this->dispatch('backupMessage', [
                'message' => 'Database restored successfully!',
                'messageType' => 'success',
                'position' => 'top-right'
            ]);
        } catch (\Exception $e) {
            $this->showingConfirmRestoreModal = false;
            $this->dispatch('backupMessage', [
                'message' => 'Restore failed: ' . $e->getMessage(),
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
        }
    }

    public function confirmDelete($fileName)
    {
        if (!auth()->user()->hasRole('admin')) {
            $this->dispatch('backupMessage', [
                'message' => 'Unauthorized action!',
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
            return;
        }

        $this->selectedBackup = $fileName;
        $this->showingConfirmDeleteModal = true;
    }

    public function deleteBackup()
    {
        if (!auth()->user()->hasRole('admin')) {
            $this->showingConfirmDeleteModal = false;
            $this->dispatch('backupMessage', [
                'message' => 'Unauthorized action!',
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
            return;
        }

        try {
            $filePath = storage_path('app/backups/' . $this->selectedBackup);
            
            if (File::exists($filePath)) {
                File::delete($filePath);
            }

            $this->loadBackups();
            $this->showingConfirmDeleteModal = false;
            $this->selectedBackup = null;
            $this->dispatch('backupMessage', [
                'message' => 'Backup deleted successfully!',
                'messageType' => 'success',
                'position' => 'top-right'
            ]);
        } catch (\Exception $e) {
            $this->showingConfirmDeleteModal = false;
            $this->dispatch('backupMessage', [
                'message' => 'Delete failed: ' . $e->getMessage(),
                'messageType' => 'error',
                'position' => 'top-right'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.settings.database-backup');
    }
}
