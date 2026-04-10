module.exports = {
  apps: [
    {
      name: 'wa-service',
      cwd: './whatsapp-service',
      script: 'server.js',
      instances: 1,
      autorestart: true,
      watch: false,
      max_memory_restart: '256M',
      env: {
        NODE_ENV: 'production',
        PORT: 3001,
      },
      log_date_format: 'YYYY-MM-DD HH:mm:ss',
      error_file: './storage/logs/wa-service-error.log',
      out_file: './storage/logs/wa-service-out.log',
      merge_logs: true,
    },
  ],
};
