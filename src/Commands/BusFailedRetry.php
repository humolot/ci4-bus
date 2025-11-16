<?php

namespace App\Commands;

use App\Models\FailedJobModel;
use App\Models\JobModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusFailedRetry extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:failed:retry';
    protected $description = 'Retry a failed job by its ID.';
    protected $usage       = 'bus:failed:retry <id>';

    public function run(array $params)
    {
        if (empty($params)) {
            CLI::error("You must provide the failed job ID.");
            return;
        }

        $id = $params[0];

        $failedModel = new FailedJobModel();
        $job = $failedModel->find($id);

        if (!$job) {
            CLI::error("Failed job not found: {$id}");
            return;
        }

        // Move job back to jobs table
        $jobModel = new JobModel();
        $jobModel->insert([
            'job_class'    => $job['job_class'],
            'payload'      => $job['payload'],
            'attempts'     => 0,
            'reserved_at'  => null,
            'available_at' => date('Y-m-d H:i:s'),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $failedModel->delete($id);

        CLI::write("Job {$id} has been re-queued.", 'green');
    }
}
