<?php

namespace Humolot\Bus\Commands;

use Humolot\Bus\Models\FailedJobModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusFailedClear extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:failed:clear';
    protected $description = 'Clears all failed jobs from the Job Bus.';

    public function run(array $params)
    {
        $failedModel = new FailedJobModel();
        $failedModel->truncate();

        CLI::write('All failed jobs have been removed.', 'green');
    }
}
