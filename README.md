# Affiliate Distance Calculator

A Laravel application that calculates and returns nearby affiliates within 100km of the Dublin office.

## Quick Start

1. Clone the repository:
```bash
git clone https://github.com/bojan-denic-public/gdc_affiliate.git
cd gdc_affiliate
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy the example environment file:
```bash
cp .env.example .env
```

4. Generate the application key:
```bash
php artisan key:generate
```

5. (Optional) Install Sail if you want to use Docker:
```bash
php artisan sail:install
```

6. Start the application with Sail:
```bash
./vendor/bin/sail up -d
```

7. Run the tests:
```bash
php artisan test
```

8. **To view the web UI in your browser, you must build the frontend assets:**
```bash
npm install
npm run dev
```

The application is now running at `http://localhost`

