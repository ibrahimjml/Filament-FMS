<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>


# Filament FMS App
## Screanshots
 ![Dashboard](https://i.postimg.cc/MK761CCf/Screenshot-2026-01-03-172519.png)
![Dashboard2](https://i.postimg.cc/ZqCzs2M7/Screenshot-2026-01-03-172604.png)
![Clients](https://i.postimg.cc/TPWHD6sB/Screenshot-2026-01-03-173034.png)
![Incomes](https://i.postimg.cc/1RMM4Xtn/Screenshot-2026-01-03-173110.png)
![Income detail](https://i.postimg.cc/4yKKJWnC/Screenshot-2026-01-03-173256.png)
![Categories](https://i.postimg.cc/WbgGg4px/Screenshot-2026-01-03-173001.png)
![Payments Schedule](https://i.postimg.cc/dt2V91Hr/Screenshot-2026-01-04-161241.png)
![Reports](https://i.postimg.cc/FRkdvPYN/Screenshot-2026-01-03-172756.png)
![Invoice](https://i.postimg.cc/V6m61vkT/Screenshot-2026-01-06-123343.png)
![Invoices](https://i.postimg.cc/8kn1XtLP/Screenshot-2026-01-06-123844.png)

## INSTALLATION

1.📦 Install dependencies
```
composer install
```
2.🛠️ Create a copy of the .env file
```
cp .env.example .env
```
3.🔑 Generate the application key
```
php artisan key:generate
```
4.📦 install node_modules
```
npm install
```
5.🚀 Compile assets with Tailwind CSS
```
npm run dev
```
6.🗄️ Set up the database
```
php artisan migrate
```
7.🔗 Create symbolic link for storage
```
php artisan storage:link
```
8.🗄️ Seed Admin credantials
```
php artisan db:seed
```
9.💻 Run the application
```
php artisan serve
```

## Admin Login
`Use these credentails to log in as admin`

- Email: admin@example.com
- Pass : password
