<?php

namespace Humolot\Bus\Commands;

use Humolot\Bus\Models\JobModel;
use Humolot\Bus\Models\FailedJobModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusWork extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:work';
    protected $description = 'Processes pending jobs from the Job Bus.';

    public function run(array $params)
    {
        // Load Bus configuration
        $config = config('Bus');

        $maxAttempts      = $config->maxAttempts ?? 3;
        $delaySeconds     = $config->delaySeconds ?? 5;
        $restartAfterJobs = $config->restartAfterJobs ?? 500;
        $sleepSeconds     = $config->sleepSeconds ?? 1;

        $jobModel    = new JobModel();
        $failedModel = new FailedJobModel();

        $processedJobs = 0;

        CLI::write("==> Job Bus Worker started\n", 'yellow');

        while (true) {

            // Automatic restart (for memory cleanup)
            if ($processedJobs >= $restartAfterJobs) {
                CLI::write("Restarting worker after {$restartAfterJobs} jobs...", 'yellow');
                exit(); // PM2 / Supervisor / Systemd will restart it
            }

            // Fetch next available job
            $job = $jobModel
                ->where('reserved_at', null)
                ->where('available_at <=', date('Y-m-d H:i:s'))
                ->orderBy('id', 'ASC')
                ->first();

            if (!$job) {
                sleep($sleepSeconds);
                continue;
            }

            // Lock the job
            $jobModel->update($job['id'], [
                'reserved_at' => date('Y-m-d H:i:s')
            ]);

            // Reload for safety
            $job = $jobModel->find($job['id']);

            try {
                $class   = $job['job_class'];
                $payload = json_decode($job['payload'], true);

                // Execute job handler
                $handler = new $class();
                $handler->handle($payload);

                // Job processed successfully
                $jobModel->delete($job['id']);
                $processedJobs++;

            } catch (\Throwable $e) {

                $attempts = $job['attempts'] + 1;

                if ($attempts >= $maxAttempts) {

                    // Move to failed jobs
                    $failedModel->insert([
                        'job_class' => $job['job_class'],
                        'payload'   => $job['payload'],
                        'exception' => $e->getMessage(),
                        'failed_at' => date('Y-m-d H:i:s'),
                    ]);

                    $jobModel->delete($job['id']);

                    CLI::error("Job FAILED permanently: {$job['job_class']}");

                } else {
                    // Requeue with delay
                    $jobModel->update($job['id'], [
                        'attempts'     => $attempts,
                        'reserved_at'  => null,
                        'available_at' => date('Y-m-d H:i:s', time() + $delaySeconds),
                    ]);

                    CLI::write("Retrying job {$job['job_class']} (attempt {$attempts}/{$maxAttempts})", 'cyan');
                }
            }
        }
    }
}
