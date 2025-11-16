<?php

namespace App\Models;

use CodeIgniter\Model;

class FailedJobModel extends Model
{
    protected $table = 'failed_jobs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'job_class', 'payload', 'exception', 'failed_at'
    ];
}
