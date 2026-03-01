# Magento 2 Layer Navigation Stock Filter

Модуль для Magento 2, який додає фільтр за статусом наявності товару (В наявності / Немає в наявності) до шарової навігації на сторінках категорій.

## Особливості

- ✅ Додає фільтр "Статус наявності" до шарової навігації
- ✅ Підтримка фільтрації товарів за статусом "В наявності" / "Немає в наявності"
- ✅ Відображення кількості товарів для кожного статусу
- ✅ Інтеграція з пошуком Magento
- ✅ Підтримка багатомовності (EN, UK)
- ✅ Сумісність з Magento 2.x

## Вимоги

- Magento 2.3.x або вище
- PHP 7.2 або вище

## Встановлення

### Метод 1: Composer (рекомендовано)

```bash
composer require mycompany/magento2-layer-navigation-filter
php bin/magento module:enable MyCompany_LayerNavigationFilter
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

### Метод 2: Ручне встановлення

1. Завантажте модуль
2. Розпакуйте в `app/code/MyCompany/LayerNavigationFilter`
3. Виконайте команди:

```bash
php bin/magento module:enable MyCompany_LayerNavigationFilter
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
```

## Використання

Після встановлення модуль автоматично додасть фільтр "Статус наявності" до шарової навігації на сторінках категорій. Користувачі зможуть фільтрувати товари за статусом:

- **В наявності** - показує тільки товари, які є в наявності
- **Немає в наявності** - показує тільки товари, яких немає в наявності

Фільтр відображає кількість товарів для кожного статусу.

## Структура модуля

```
MyCompany/LayerNavigationFilter/
├── Model/
│   ├── Layer/
│   │   └── Filter/
│   │       ├── Item.php          # Кастомна логіка для URL фільтрів
│   │       └── Stock.php         # Основний клас фільтра наявності
│   └── StockFilterState.php      # Зберігання стану фільтра
├── Plugin/
│   ├── FilterList.php            # Додає фільтр до списку фільтрів
│   └── SearchResultFilter.php    # Фільтрація результатів пошуку
├── etc/
│   ├── frontend/
│   │   └── di.xml               # Конфігурація dependency injection
│   └── module.xml               # Конфігурація модуля
├── i18n/
│   ├── en_US.csv               # Англійські переклади
│   └── uk_UA.csv               # Українські переклади
└── registration.php            # Реєстрація модуля
```

## Технічні деталі

### Як це працює

1. **FilterList Plugin** - додає фільтр наявності до списку фільтрів шарової навігації
2. **Stock Filter** - реалізує логіку фільтрації, підраховує кількість товарів для кожного статусу
3. **StockFilterState** - зберігає поточний стан фільтра (вибраний статус)
4. **SearchResultFilter Plugin** - застосовує фільтр до результатів пошуку

### Залежності

Модуль залежить від:
- `Magento_Catalog`
- `Magento_CatalogInventory`
- `Magento_LayeredNavigation`

## Видалення

```bash
php bin/magento module:disable MyCompany_LayerNavigationFilter
php bin/magento setup:upgrade
php bin/magento cache:flush
```

Потім видаліть директорію модуля або виконайте:

```bash
composer remove mycompany/magento2-layer-navigation-filter
```

## Ліцензія

Див. файл [LICENSE](LICENSE)

## Підтримка

Для повідомлення про помилки або запитів нових функцій, будь ласка, створіть issue на GitHub.

## Автор

MyCompany
