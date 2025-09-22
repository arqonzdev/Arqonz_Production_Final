# Deployment Guide

## Environment Configuration System

This project uses environment-based configuration to handle different settings for staging and production environments.

### Files Structure

```
config/
├── env.example              # Template for environment variables
└── local/
    ├── .env                 # Environment-specific secrets (not in git)
    └── database.yaml        # Database configuration (not in git)
```

### Setup for New Environment

1. **Copy the environment template:**
   ```bash
   cp config/env.example config/local/.env
   ```

2. **Edit the environment file:**
   ```bash
   nano config/local/.env
   ```

3. **Fill in the actual values:**
   ```bash
   # reCAPTCHA Configuration
   RECAPTCHA_SITE_KEY=your_actual_site_key
   RECAPTCHA_SECRET_KEY=your_actual_secret_key
   
   # Gupshup WhatsApp API Configuration
   GUPSHUP_API_KEY=your_actual_api_key
   GUPSHUP_SOURCE_NUMBER=your_actual_whatsapp_number
   GUPSHUP_SOURCE_NAME=your_actual_source_name
   
   # Razorpay Configuration
   RAZORPAY_KEY_ID=your_actual_razorpay_key_id
   RAZORPAY_KEY_SECRET=your_actual_razorpay_secret
   
   # OpenAI Configuration
   OPENAI_API_KEY=your_actual_openai_api_key
   
   # WhatsApp API Configuration
   WHATSAPP_API_URL=your_actual_whatsapp_api_url
   WHATSAPP_API_KEY=your_actual_whatsapp_api_key
   
   # Domain Configuration
   APP_DOMAIN=your-domain.com
   APP_URL=https://your-domain.com
   ```
   
   **Note:** `APP_ENV` and `APP_DEBUG` are already defined in the main `.env` file and should not be duplicated.

### Deployment Process

#### From GitHub to Production Server

1. **Clone/Pull the repository:**
   ```bash
   git clone https://github.com/your-username/your-repo.git
   # or
   git pull origin main
   ```

2. **Set up environment configuration:**
   ```bash
   cp config/env.example config/local/.env
   nano config/local/.env  # Fill in production values
   ```

3. **Configure database:**
   ```bash
   nano config/local/database.yaml  # Set production database
   ```

4. **Install dependencies:**
   ```bash
   composer install --no-dev
   ```

5. **Clear cache:**
   ```bash
   php bin/console cache:clear
   ```

### Environment-Specific Values

#### Staging Environment
- Domain: arqonz.in
- reCAPTCHA: Staging keys
- Gupshup: Staging API credentials
- Database: Local or staging database

#### Production Environment
- Domain: your-production-domain.com
- reCAPTCHA: Production keys
- Gupshup: Production API credentials
- Database: Production database

### Security Notes

- Never commit .env files to version control
- Use different API keys for staging and production
- Keep database credentials secure
- Regularly rotate API keys and secrets
