<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        // Get all backup files
        $backups = [];
        $files = Storage::disk('local')->files('backups');
        
        // Also check Laravel-backup default location
        $backupFiles = Storage::disk('local')->files('Laravel');
        
        foreach ($backupFiles as $file) {
            if (str_contains($file, '.zip')) {
                $backups[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => Storage::disk('local')->size($file),
                    'created_at' => Storage::disk('local')->lastModified($file),
                    'type' => 'database'
                ];
            }
        }
        
        // Sort backups by date (newest first)
        usort($backups, function($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });

        $stats = [
            'total' => count($backups),
            'total_size' => array_sum(array_column($backups, 'size')),
            'last_backup' => $backups[0]['created_at'] ?? null,
        ];

        $backupAvailable = class_exists(\ZipArchive::class);
        return view('admin.backup.index', compact('backups', 'stats', 'backupAvailable'));
    }

    public function create(Request $request)
    {
        if (!class_exists(\ZipArchive::class)) {
            return back()->with('error', 'Backups require the PHP zip extension (ZipArchive). Enable php-zip, then retry.');
        }
        try {
            // Get type of backup
            $type = $request->type ?? 'database';
            
            // Run backup command
            Artisan::call('backup:run', [
                '--only-db' => $type === 'database',
                '--only-files' => $type === 'files',
            ]);

            $output = Artisan::output();

            return redirect()->route('admin.backup.index')
                ->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download($filename)
    {
        $filePath = 'Laravel/' . $filename;
        
        if (!Storage::disk('local')->exists($filePath)) {
            return redirect()->back()->with('error', 'Backup file not found!');
        }

        return Storage::disk('local')->download($filePath);
    }

    public function delete($filename)
    {
        $filePath = 'Laravel/' . $filename;
        
        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);
            return redirect()->route('admin.backup.index')
                ->with('success', 'Backup deleted successfully!');
        }

        return redirect()->route('admin.backup.index')
            ->with('error', 'Backup file not found!');
    }

    public function deleteAll()
    {
        $files = Storage::disk('local')->files('Laravel');
        foreach ($files as $file) {
            Storage::disk('local')->delete($file);
        }

        return redirect()->route('admin.backup.index')
            ->with('success', 'All backups deleted successfully!');
    }

    public function restore($filename)
    {
        if (!class_exists(\ZipArchive::class)) {
            return back()->with('error', 'Restore inspection requires the PHP zip extension (ZipArchive).');
        }

        $filePath = 'Laravel/' . basename($filename);
        if (!Storage::disk('local')->exists($filePath)) {
            return back()->with('error', 'Backup file not found.');
        }

        // A web-triggered database restore is intentionally not automated. It can replace
        // live data while HTTP requests are in-flight and the installed backup package does
        // not provide a portable restore command. Download the archive and restore it through
        // your deployment/DB tooling after taking the store offline.
        return back()->with('error', 'One-click web restore is disabled for safety. Download this archive and restore it through your database/deployment tooling while the store is in maintenance mode.');
    }

    public function schedule()
    {
        return view('admin.backup.schedule');
    }

    public function updateSchedule(Request $request)
    {
        $validated = $request->validate([
            'frequency' => 'required|in:daily,weekly,monthly',
            'time' => 'required|date_format:H:i',
            'keep_backups' => 'required|integer|min:1|max:100',
        ]);

        // Save to settings
        \App\Models\Setting::set('backup_frequency', $request->frequency);
        \App\Models\Setting::set('backup_time', $request->time);
        \App\Models\Setting::set('backup_keep', $request->keep_backups);

        return redirect()->route('admin.backup.schedule')
            ->with('success', 'Backup schedule updated successfully!');
    }
}