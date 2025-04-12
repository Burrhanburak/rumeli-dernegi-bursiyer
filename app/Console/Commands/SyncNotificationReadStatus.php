<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncNotificationReadStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:sync-read-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize is_read column with read_at for all notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing notification read status...');

        // Update is_read to true for all notifications with a read_at timestamp
        $updated = DB::table('laravel_notifications')
            ->whereNotNull('read_at')
            ->update(['is_read' => true]);

        // Update is_read to false for all notifications without a read_at timestamp
        $updatedNull = DB::table('laravel_notifications')
            ->whereNull('read_at')
            ->update(['is_read' => false]);

        $this->info("Synced read status for $updated read notifications and $updatedNull unread notifications.");
        
        return Command::SUCCESS;
    }
}
