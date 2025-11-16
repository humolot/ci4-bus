# CodeIgniter 4 Job Bus  
A full-featured asynchronous Job Bus and Queue Worker system for CodeIgniter 4 — similar to Laravel Queues & Horizon, but designed specifically for CI4.

This package provides a clean, scalable, production-ready job processing system with:
- Job dispatching
- Delayed jobs
- Automatic retries
- Failed job handling
- Custom worker
- PM2/Supervisor support
- Job generators
- CLI tools
- Configurable behavior
- Perfect for heavy background processing (CSV import, emails, PDFs, API jobs, etc.)

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

Run the installer:

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

Your job will look like:

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

## 🚀 Dispatching Jobs

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

Start processing jobs:

```
php spark bus:work
```

## 🔥 Production Worker Setup

### PM2 (recommended)

```
npm install pm2 -g
pm2 start php --name ci4_bus -- spark bus:work
pm2 save
pm2 startup
```

### Supervisor (Linux)

Create `/etc/supervisor/conf.d/ci4_bus.conf`:

```
[program:ci4_bus]
command=/usr/bin/php /var/www/project/spark bus:work
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/ci4_bus.log
```

Reload Supervisor:

```
supervisorctl reread
supervisorctl update
```

### Windows Service (NSSM)

```
nssm install CI4BusWorker
```

Set:

```
Application: C:\xampp\php\php.exe
Arguments: spark bus:work
```

## ❌ Failed Job Management

### List failed jobs

```
php spark bus:failed
```

### Retry a single job

```
php spark bus:failed:retry 12
```

### Retry all failed jobs

```
php spark bus:retry-all
```

### Clear failed jobs

```
php spark bus:failed:clear
```

## 🧹 Clearing Pending Jobs

```
php spark bus:clear
```

## 📁 Folder Structure

```
app/
 ├ Commands/
 │    ├ BusWork.php
 │    ├ BusClear.php
 │    ├ BusFailed.php
 │    ├ BusFailedRetry.php
 │    ├ BusRetryAll.php
 │    └ BusMake.php
 ├ Config/
 │    └ Bus.php
 ├ Jobs/
 │    ├ JobInterface.php
 │    ├ BaseJob.php
 │    └ ...
 ├ Libraries/
 │    └ Bus.php
 ├ Models/
 │    ├ JobModel.php
 │    └ FailedJobModel.php
```
## 📜 License

MIT License.
