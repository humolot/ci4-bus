<?php

namespace Humolot\Bus\Commands;

use Humolot\Bus\Models\JobModel;
use Humolot\Bus\Models\FailedJobModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusRetryAll extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:retry-all';
    protected $description = 'Re-queue all failed jobs.';

    public function run(array $params)
    {
        $failedModel = new FailedJobModel();
        $jobModel    = new JobModel();

        $failedJobs = $failedModel->findAll();

        if (!$failedJobs) {
            CLI::write("There are no failed jobs.", 'green');
            return;
        }

        foreach ($failedJobs as $job) {
            $jobModel->insert([
                'job_class'    => $job['job_class'],
                'payload'      => $job['payload'],
                'attempts'     => 0,
                'reserved_at'  => null,
                'available_at' => date('Y-m-d H:i:s'),
                'created_at'   => date('Y-m-d H:i:s'),
            ]);

            $failedModel->delete($job['id']);
        }

        CLI::write("All failed jobs have been re-queued.", 'green');
    }
}
