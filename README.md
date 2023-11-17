## Installation

buka terminal anda

```bash
git clone https://github.com/HelfanzaNanda/test-api-herca.git

# masuk project
cd test-api-herca

# pastikan php sudah 8
# install vendor
composer install

# copy .env.example jadi .env
cp .env.example .env

# generatae APP_KEY
php artisan key:generate
```

buka .env setting database anda
```bash
# DB_DATABASE
# DB_USERNAME
# DB_PASSWORD
```

buka lagi terminal anda, masuk keproject tersebut
```bash
# migrasi db dan data
php artisan migrate --seed

# symlink
php artisan storage:link

# running
php artisan serve
```

 
## CREDENTIALS

```python
# ADMIN
username : admin@example.com
password : password

# CUSTOMER
username : customer@example.com
password : password
```