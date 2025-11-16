<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusInstall extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:install';
    protected $description = 'Installs the Job Bus structure for CodeIgniter 4.';

    public function run(array $params)
    {
        CLI::write("==> Installing the Job Bus structure...\n", 'yellow');

        // ----------------------------------------------------
        // 1. Create the app/Jobs directory
        // ----------------------------------------------------
        $jobsPath = APPPATH . 'Jobs/';

        if (!is_dir($jobsPath)) {
            mkdir($jobsPath);
            CLI::write("[✔] Directory created: app/Jobs", 'green');
        } else {
            CLI::write("[i] app/Jobs already exists.", 'cyan');
        }

        // ----------------------------------------------------
        // 2. Create JobInterface.php
        // ----------------------------------------------------
        $jobInterfaceFile = $jobsPath . 'JobInterface.php';

        if (!file_exists($jobInterfaceFile)) {
            file_put_contents($jobInterfaceFile, $this->jobInterfaceTemplate());
            CLI::write("[✔] Created: app/Jobs/JobInterface.php", 'green');
        } else {
            CLI::write("[i] JobInterface.php already exists.", 'cyan');
        }

        // ----------------------------------------------------
        // 3. Create BaseJob.php
        // ----------------------------------------------------
        $baseJobFile = $jobsPath . 'BaseJob.php';

        if (!file_exists($baseJobFile)) {
            file_put_contents($baseJobFile, $this->baseJobTemplate());
            CLI::write("[✔] Created: app/Jobs/BaseJob.php", 'green');
        } else {
            CLI::write("[i] BaseJob.php already exists.", 'cyan');
        }

        // ----------------------------------------------------
        // 4. Create migrations
        // ----------------------------------------------------
        $migrationPath = APPPATH . 'Database/Migrations/';
        $timestamp     = date('Y-m-d-His');

        // jobs migration
        $jobsMigrationFile = $migrationPath . $timestamp . '_create_jobs_table.php';

        if (!glob($migrationPath . '*_create_jobs_table.php')) {
            file_put_contents($jobsMigrationFile, $this->jobsMigrationTemplate());
            CLI::write("[✔] Migration created: create_jobs_table", 'green');
        } else {
            CLI::write("[i] Migration create_jobs_table already exists.", 'cyan');
        }

        // failed jobs migration
        $failedJobsMigrationFile = $migrationPath . $timestamp . '_create_failed_jobs_table.php';

        if (!glob($migrationPath . '*_create_failed_jobs_table.php')) {
            file_put_contents($failedJobsMigrationFile, $this->failedJobsMigrationTemplate());
            CLI::write("[✔] Migration created: create_failed_jobs_table", 'green');
        } else {
            CLI::write("[i] Migration create_failed_jobs_table already exists.", 'cyan');
        }

        // ----------------------------------------------------
        // 5. Create Config/Bus.php
        // ----------------------------------------------------
        $configPath = APPPATH . 'Config/Bus.php';

        if (!file_exists($configPath)) {
            file_put_contents($configPath, $this->busConfigTemplate());
            CLI::write("[✔] Created: app/Config/Bus.php", 'green');
        } else {
            CLI::write("[i] Config/Bus.php already exists.", 'cyan');
        }

        CLI::write("\n==> Job Bus installed successfully!", 'green');
        CLI::write("Next steps:\n", 'yellow');
        CLI::write("   > php spark migrate", 'white');
        CLI::write("   > php spark bus:work", 'white');
    }

    // ============================================================
    // Templates
    // ============================================================

    private function jobInterfaceTemplate()
    {
        return <<<PHP
<?php

namespace App\Jobs;

interface JobInterface
{
    public function handle(array \$data);
}
PHP;
    }

    private function baseJobTemplate()
    {
        return <<<PHP
<?php

namespace App\Jobs;

abstract class BaseJob implements JobInterface
{
    public int \$attempts = 0;

    public function handle(array \$data)
    {
        // Override this method in the child job class.
    }
}
PHP;
    }

    private function busConfigTemplate()
    {
        return <<<PHP
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Bus extends BaseConfig
{
    public int \$maxAttempts       = 3;
    public int \$delaySeconds      = 5;
    public int \$restartAfterJobs  = 500;
    public int \$sleepSeconds      = 1;
}
PHP;
    }

    private function jobsMigrationTemplate()
    {
        return <<<PHP
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobsTable extends Migration
{
    public function up()
    {
        \$this->forge->addField([
            'id'           => ['type' => 'BIGINT', 'auto_increment' => true],
            'job_class'    => ['type' => 'VARCHAR', 'constraint' => '255'],
            'payload'      => ['type' => 'LONGTEXT'],
            'attempts'     => ['type' => 'INT', 'default' => 0],
            'reserved_at'  => ['type' => 'DATETIME', 'null' => true],
            'available_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        \$this->forge->addKey('id', true);
        \$this->forge->createTable('jobs');
    }

    public function down()
    {
        \$this->forge->dropTable('jobs');
    }
}
PHP;
    }

    private function failedJobsMigrationTemplate()
    {
        return <<<PHP
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFailedJobsTable extends Migration
{
    public function up()
    {
        \$this->forge->addField([
            'id'        => ['type' => 'BIGINT', 'auto_increment' => true],
            'job_class' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'payload'   => ['type' => 'LONGTEXT'],
            'exception' => ['type' => 'LONGTEXT'],
            'failed_at' => ['type' => 'DATETIME', 'null' => false],
        ]);

        \$this->forge->addKey('id', true);
        \$this->forge->createTable('failed_jobs');
    }

    public function down()
    {
        \$this->forge->dropTable('failed_jobs');
    }
}
PHP;
    }
}
