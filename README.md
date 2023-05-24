# Стажировочное задание

## Запуск
1. make docker-run
2. make schema-update
3. в директории symfony выполнить команду composer install

## Отправка и прием сообщений
1. make run_consumer - для прослушивания
2. make send_message - отправка сообщений

## Роуты
- {{host}}/products - возвращяет список всех продуктов из БД

- {{host}}/products/{sku} - возвращяет продукт по идентификаторы из БД

- {{host}}/products/by-name/{name} - возвращает продукты по имени из эластика

- {{host}}/products/by-description/{description} - возвращает продукты по их описанию из эластика

- {{host}}/products/by-category/{category} - возвращает продукты по категории из БД