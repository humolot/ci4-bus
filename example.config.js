module.exports = {
  apps: [{
    name: 'ci4-bus-worker',
    script: 'php',
    args: 'spark bus:work',
    cwd: 'C:\\xampp\\htdocs\\project',
    interpreter: 'none',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '200M',
    env: {
      CI_ENVIRONMENT: 'production'
    }
  }]
};