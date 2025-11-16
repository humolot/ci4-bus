<?php

namespace Humolot\Bus\Commands;

use Humolot\Bus\Models\FailedJobModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusFailed extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:failed';
    protected $description = 'Displays all failed jobs.';

    public function run(array $params)
    {
        $failed = (new FailedJobModel())->findAll();

        if (empty($failed)) {
            CLI::write("No failed jobs found.", 'green');
            return;
        }

        foreach ($failed as $job) {
            CLI::write("------------------------------------------------------------", 'yellow');
            CLI::write("ID:         {$job['id']}", 'white');
            CLI::write("Class:      {$job['job_class']}", 'white');
            CLI::write("Failed At:  {$job['failed_at']}", 'white');
            CLI::write("Exception:\n{$job['exception']}", 'red');
            CLI::write("------------------------------------------------------------\n", 'yellow');
        }
    }
}
