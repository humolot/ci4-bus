<?php

namespace Humolot\Bus\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class BusMake extends BaseCommand
{
    protected $group       = 'Bus';
    protected $name        = 'bus:make';
    protected $description = 'Creates a new Job class.';
    protected $usage       = 'bus:make <JobName>';

    public function run(array $params)
    {
        if (empty($params)) {
            CLI::error("You must provide a Job name.");
            return;
        }

        $name = ucfirst($params[0]);

        $path = APPPATH . "Jobs/{$name}.php";

        if (file_exists($path)) {
            CLI::error("Job {$name} already exists.");
            return;
        }

        file_put_contents($path, $this->template($name));

        CLI::write("Job created: app/Jobs/{$name}.php", 'green');
    }

    private function template($name)
    {
        return <<<PHP
<?php

namespace App\Jobs;

class {$name} extends BaseJob
{
    public function handle(array \$data)
    {
        // Your job logic here
    }
}
PHP;
    }
}
