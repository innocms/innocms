<?php
/**
 * Copyright (c) Since 2024 InnoCMS - All Rights Reserved
 *
 * @link       https://www.innocms.com
 * @author     InnoCMS <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace InnoCMS\Install\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use InnoCMS\Install\Libraries\Creator;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'innocms:install
        {--admin-email=admin@innocms.com : Admin account email}
        {--admin-pass=innocms1234 : Admin account password}
        {--theme= : Theme code to activate right after install}
        {--demo : Import demo content bundled with the chosen theme}
        {--force : Reinstall even when the install lock already exists}';

    /**
     * The console command description.
     */
    protected $description = 'Install InnoCMS from CLI (migrate, seed, admin, theme, lock)';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Throwable
     */
    public function handle(): int
    {
        if (has_install_lock() && ! $this->option('force')) {
            $this->error('InnoCMS is already installed (storage/installed exists). Use --force to reinstall.');

            return self::FAILURE;
        }

        $data = [
            'theme'          => (string) $this->option('theme'),
            'load_demo'      => (bool) $this->option('demo'),
            'admin_email'    => (string) $this->option('admin-email'),
            'admin_password' => (string) $this->option('admin-pass'),
        ];

        try {
            DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $this->error('Cannot connect to the database: '.$e->getMessage());
            $this->line('Configure DB_* entries in .env first, then run this command again.');

            return self::FAILURE;
        }

        $this->info('Installing InnoCMS...');

        Creator::getInstance()->setup($data);

        // Some packs (e.g. member) register their migrations only when the
        // install lock exists, so they stayed invisible during Creator::setup()
        // and cannot be picked up by a migrate call in this process either —
        // providers already booted without the lock. Run migrate in a fresh
        // subprocess where the lock now exists and all packs register.
        $artisan = base_path('artisan');
        $process = new Process([PHP_BINARY, $artisan, 'migrate', '--force']);
        $process->setTimeout(300);
        $process->mustRun();
        $this->line(trim($process->getOutput()));

        $this->info('Installed. Admin: '.$data['admin_email']);

        return self::SUCCESS;
    }
}
