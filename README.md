# Maker Checker Payment SYstem
A payment solution for Dantown wallet to enable users approve and reject transactions.


<p>
  <blockquote style="color:red">
    **Please follow the steps below to setup the application on your system** 
  </blockquote>
</p>  

## Required Versions
-PHP 8.2

## Installation Steps

- Clone project
- Run ```composer install``` for the main project
- Rename .env.example to .env
- Create you database and set dbname, username and password on the new .env file
- Generate your laravel key : ```php artisan key:generate```
- Run ```php artisan app:setup-command``` to set up the application and database.
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
