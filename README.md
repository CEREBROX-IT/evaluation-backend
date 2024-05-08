# Evaluation REST API
![php](https://img.shields.io/badge/php-%fcc803.svg?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel10-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
[![mysql](https://img.shields.io/badge/mysql-2d97d2?style=for-the-badge&logo=mysql&logoColor=orange)](https://www.mysql.com/)
## Basic setup on your local device by following the step.

## 1. Clone the Repository:
- Open your terminal or command prompt and navigate to the directory where you want to store your Laravel project. Then, use the git clone command to clone the repository from GitHub. 

```bash
# clone the repositoy
https://github.com/CEREBROX-IT/evaluation-backend.git

# navigate to the backend directory
cd evaluation-backend
```
## 2. Tools and others:
- Make sure to have <strong>Mysql</strong> installed on your device and tool to have access to database like <strong>phpmyadmin</strong>.
- Make sure <strong>composer</strong> is installed.
<a href="https://getcomposer.org/">https://getcomposer.org</a>
- Create Database name <strong>"evaluation-db"</strong> manually. 

## 3. Install Composer Dependencies:
- Laravel uses Composer to manage its dependencies. Run the following command to install all the required dependencies specified in the composer.json file:
```bash
composer install
```
## 4. Create a Copy of the Environment File:
- Laravel requires an <strong>.env</strong> file for configuration. Make a copy of the .env.example file and rename it to .env. You can do this with the following command:
```bash
cp .env.example .env
```
## 5. Generate Application Key:
- Run the following command to generate a unique application key for your Laravel application. (Add this on your .env file)
```bash
# generate application key
php artisan key:generate
```
## 6. Set Up Environment Configuration:
- Open the <strong>.env</strong> file in a text editor and configure your environment variables, such as database connection settings, mail settings, etc.
## 7. Run Database Migrations:
- Run the database migrations to create the necessary tables:
```bash
php artisan migrate --path=database/migrations/2024_04_19_061114_create_session_table.php
php artisan migrate
```
## 8. Serve the Application:
- Finally, you can serve your Laravel application using the built-in PHP development server or any other web server you prefer. To use the PHP development server, run the following command:
```bash
php artisan serve
```
- Visit the endpoint below to check if laravel is Online
<a href="http://127.0.0.1:8000/api/">http://127.0.0.1:8000/api/</a>

php artisan migrate:refresh --seed
