#!/bin/bash

# Build the client
echo "Building client..."
cd client
npm install
npm run build
cd ..

# Install server dependencies
echo "Installing server dependencies..."
npm install

# Create production environment file
echo "Creating production environment file..."
cat > .env << EOL
NODE_ENV=production
PORT=5000
MONGODB_URI=your_mongodb_uri
JWT_SECRET=your_jwt_secret
CLIENT_URL=https://веата.com
SMTP_HOST=your_smtp_host
SMTP_PORT=your_smtp_port
SMTP_USER=your_smtp_user
SMTP_PASS=your_smtp_password
EOL

# Create PM2 ecosystem file
echo "Creating PM2 configuration..."
cat > ecosystem.config.js << EOL
module.exports = {
  apps: [{
    name: 'veata',
    script: 'server.js',
    instances: 'max',
    exec_mode: 'cluster',
    autorestart: true,
    watch: false,
    max_memory_restart: '1G',
    env: {
      NODE_ENV: 'production'
    }
  }]
};
EOL

echo "Build complete! Ready for deployment." 