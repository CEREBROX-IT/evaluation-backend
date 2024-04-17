# Evaluation REST API
![php](https://img.shields.io/badge/php-%fcc803.svg?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
[![mysql](https://img.shields.io/badge/mysql-2d97d2?style=for-the-badge&logo=mysql&logoColor=orange)](https://www.mysql.com/)
## Basic setup on your local device by following the step.

- Clone the repository using HTTPS. Open the terminal and run the command below to save the repository on your local.

```bash
# clone the repositoy
https://github.com/CEREBROX-IT/evaluation-backend.git

# navigate to the backend directory
cd evaluation-backend
```
- Make sure to have Mysql installed on your device and tool to have access to database like phpmyadmin.
- Make sure composer is installed.
- Create Database name "evaluation-db".

```bash
# install the PHP dependencies using Composer
composer install
```

- Laravel requires an .env file for configuration. Make a copy of the .env.example file and rename it to .env. You can do this with the following command:
```bash
cp .env.example .env
```

```bash
# generate application key
php artisan key:generate

# run the command to create migration
php artisan migrate

# Start the Development Server:
php artisan serve
```

