<?php

namespace App\Commands;

use App\Models\JobModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusClear extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:clear';
    protected $description = 'Clears all pending jobs from the Job Bus.';

    public function run(array $params)
    {
        $jobModel = new JobModel();
        $jobModel->truncate();

        CLI::write('All pending jobs have been removed.', 'green');
    }
}
