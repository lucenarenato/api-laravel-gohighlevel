<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# comands

```sh
./vendor/bin/sail artisan --version
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan migrate:refresh
./vendor/bin/sail artisan db:seed
./vendor/bin/sail artisan livewire:layout
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm install && npm run dev 
```

# MailHog docker-compose env 
- with local storage
- web ui on port 8025 with login "test"  and password "test" 
- smtp on port 2025 with login="" and password=""

## Change login and password

 `printf "login:" > mailhog.auth`
 
 `printf "$(sudo docker exec  mailhog /bin/sh -c "MailHog bcrypt password")" >> mailhog.auth`
  - mailhog - name of docker container mailhog

 `cat mailhog.auth`
