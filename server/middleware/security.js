const helmet = require('helmet');
const mongoSanitize = require('express-mongo-sanitize');
const xss = require('xss-clean');
const rateLimit = require('express-rate-limit');

// Rate limiting
const limiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutes
  max: 100, // limit each IP to 100 requests per windowMs
  message: 'Too many requests from this IP, please try again after 15 minutes'
});

// Security middleware
const securityMiddleware = [
  // Set security HTTP headers
  helmet(),
  
  // Sanitize data
  mongoSanitize(),
  
  // Prevent XSS attacks
  xss(),
  
  // Rate limiting
  limiter
];

module.exports = securityMiddleware; 