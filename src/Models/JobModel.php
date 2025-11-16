<?php

namespace Humolot\Bus\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table      = 'jobs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'job_class',
        'payload',
        'attempts',
        'available_at',
        'reserved_at',
        'created_at'
    ];
}
