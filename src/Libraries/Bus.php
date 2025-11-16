<?php

namespace Humolot\Bus;

use Humolot\Bus\Models\JobModel;
use DateTime;

class Bus
{
    /**
     * Dispatch a job to the Job Bus.
     */
    public static function dispatch(string $jobClass, array $payload = [], int $delaySeconds = 0): int
    {
        $jobModel = new JobModel();

        $availableAt = (new DateTime("+{$delaySeconds} seconds"))
            ->format('Y-m-d H:i:s');

        return $jobModel->insert([
            'job_class'    => $jobClass,
            'payload'      => json_encode($payload),
            'attempts'     => 0,
            'reserved_at'  => null,
            'available_at' => $availableAt,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Dispatch a job that should run later.
     */
    public static function later(int $seconds, string $jobClass, array $payload = []): int
    {
        return self::dispatch($jobClass, $payload, $seconds);
    }

    /**
     * Bulk dispatch many jobs at once.
     */
    public static function bulk(array $jobs): array
    {
        $ids = [];

        foreach ($jobs as $job) {
            $ids[] = self::dispatch(
                $job['class'],
                $job['payload'] ?? [],
                $job['delay'] ?? 0
            );
        }

        return $ids;
    }
}
