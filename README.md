# CodeIgniter 4 Job Bus  
A full-featured asynchronous Job Bus and Queue Worker system for CodeIgniter 4 — similar to Laravel Queues & Horizon, but built specifically for CI4.

Now installable via Composer:

```
composer require humolot/ci4-bus
```

## ✨ Features

- 🚀 Dispatch async jobs using `Bus::dispatch()`
- ⏰ Delayed jobs (`Bus::later()`)
- 🔁 Automatic retry logic
- ❌ Failed job storage & management
- 🧹 Commands to clear jobs and failed jobs
- 🔄 Retry individual or all failed jobs
- 🛠 `bus:make` to scaffold new Job classes
- 🧵 Custom worker: `bus:work`
- 🔧 Configurable (attempts, delay, sleep, restart cycle)
- 🖥️ Compatible with PM2, Supervisor, Systemd, NSSM (Windows)

## 📦 Installation

```
composer require humolot/ci4-bus
```

Run installer:

```
php spark bus:install
```

This will create:

```
app/Jobs/
app/Jobs/JobInterface.php
app/Jobs/BaseJob.php
app/Config/Bus.php
migrations for jobs & failed_jobs
```

Run migrations:

```
php spark migrate
```

## ⚙ Configuration (app/Config/Bus.php)

```
class Bus extends BaseConfig
{
    public int $maxAttempts       = 3;
    public int $delaySeconds      = 5;
    public int $restartAfterJobs  = 500;
    public int $sleepSeconds      = 1;
}
```

## 🛠 Creating Jobs

Generate:

```
php spark bus:make SendEmailJob
```

Your job:

```
namespace App\Jobs;

class SendEmailJob extends BaseJob
{
    public function handle(array $data)
    {
        // Your logic here
    }
}
```

## 🚀 Using the Package (Correct Path via Composer)

Import the Bus class from vendor:

```
use Humolot\Bus\Bus;

Bus::dispatch(\App\Jobs\SendEmailJob::class, ['id' => 10]);
```

### Dispatch immediately

```
Bus::dispatch(\App\Jobs\SendEmailJob::class, [
    'email' => 'user@example.com'
]);
```

### Dispatch later

```
Bus::later(60, \App\Jobs\SendEmailJob::class, [
    'email' => 'user@example.com'
]);
```

### Bulk dispatch

```
Bus::bulk([
    ['class' => JobA::class, 'payload' => [...]],
    ['class' => JobB::class, 'payload' => [...], 'delay' => 10]
]);
```

## 🧵 Running the Worker

```
php spark bus:work
```

## 🔥 Production Worker Setup

### PM2

```
npm install pm2 -g
pm2 start php --name ci4_bus -- spark bus:work
pm2 save
pm2 startup
```

### Supervisor

```
[program:ci4_bus]
command=/usr/bin/php /var/www/project/spark bus:work
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/ci4_bus.log
```

Reload:

```
supervisorctl reread
supervisorctl update
```

### Windows (NSSM)

```
nssm install CI4BusWorker
```

## ❌ Failed Job Management

```
php spark bus:failed
php spark bus:failed:retry 12
php spark bus:retry-all
php spark bus:failed:clear
```

## 🧹 Clearing Jobs

```
php spark bus:clear
```

## 📁 Folder Structure

Because the package is now installed via Composer, the core lives in:

```
vendor/humolot/ci4-bus/src
```

Your application files remain in:

```
app/Jobs/
app/Config/Bus.php
app/Models/
app/Libraries/Bus.php (auto-published)
```

## 📜 License

MIT License.
