# PHPers 2022 warsztaty "Jak rozbudowywać i integrować się z SaaS na przykładzie Shopware Apps?"

Repozytorium powstało na podstawie szablonu bazowego Shopware App (https://github.com/shopwareLabs/AppExample).

## Co zawiera bazowa wersja aplikacji

- Wbudowany proces rejestracji aplikacji w Shopware
- API SDK do komunikacji z API Shopware (https://github.com/vienthuong/shopware-php-sdk)

## Wymagania

- Docker
- ddev (https://ddev.readthedocs.io/en/stable/#linux-macos-and-windows-wsl2-install-script)

## Instalacja

Sklonuj repozytorium i uruchom komendę, aby zainstalować wszystkie zależności:

```bash
    composer install
```

Następnie uruchom środowisko developerskie za pomocą komendy:

```bash
    ddev start
```

Po uruchomieniu środowiska wykonaj migracje za pomocą komendy:

```bash
    php bin/console doctrine:migrations:migrate
```
