# CMS-модуль для MODX (CustomForm)

## Опис
Компонент MODX Revolution додає снипет форми заявки з полями
«Ім'я», «Телефон», «Email», «Повідомлення», server-side валідацією,
rate-limit захистом (проти флуду з однієї IP), honeypot-полем проти
спам-ботів, збереженням заявок через xPDO та (опційно) передачею в CRM.

## Вимоги
- MODX Revolution 3.1.2-pl
- PHP 8.2+
- MySQL/MariaDB

## Локальне оточення для перевірки (Docker)
1. `docker compose up -d --build` з кореня репозиторію
2. MODX manager: http://localhost:8080/manager/
3. Adminer (перегляд БД): http://localhost:8081

## Встановлення
1. Скопіюйте `core/components/customform/` і `assets/components/customform/`
   у відповідні каталоги MODX-сайту.
2. Встановіть transport-пакет `customform-1.0.0.transport.zip` через
   Пакети → Завантажити пакет (таблицю і реєстрацію пакета створює резолвер
   `_build/resolvers/resolve.schema.php` автоматично при встановленні).
3. Створіть Snippet-елемент `CustomForm` в менеджері MODX як **static file**,
   що вказує на `core/components/customform/elements/snippets/snippet.customform.php`
   (аналогічно чанки `customform.form` і `customform.success` →
   `core/components/customform/elements/chunks/`).
4. Розмістіть снипет на потрібній сторінці: `[[!CustomForm]]`
   (обов'язково з `!` — снипет має бути некешованим, бо працює з `$_POST`).

## Перевірка роботи
1. Відкрийте сторінку з формою → заповніть і відправте
2. Заявка з'являється в таблиці `modx_customform_submissions`
3. Невалідні дані (ім'я з цифрами, некоректний телефон) - форма повертає помилки,
   запис в БД не створюється
4. Заповнене приховане поле `website` (honeypot) - тихий "успіх" без запису в БД
5. 4 швидкі відправки з однієї IP поспіль - 4-та блокується rate limit'ом

## Архітектура
| Файл | Відповідальність |
|---|---|
| `model/schema/customform.mysql.schema.xml` | xPDO-схема таблиці заявок |
| `model/customform/` | Згенеровані xPDO-класи (namespace `customform`) |
| `elements/snippets/snippet.customform.php` | Форма, валідація, rate limit, збереження, виклик CRM |
| `elements/chunks/customform.*.tpl` | HTML-шаблони форми та успіху |
| `_build/resolvers/resolve.schema.php` | Реєстрація пакета і створення таблиці при встановленні |
| `_build/build.transport.php` | Збірка дистрибутивного transport-пакета |
| `assets/js/customform.js`, `assets/css/customform.css` | Клієнтські ресурси форми |

## Безпека
- Вхідні дані проходять `$modx->sanitizeString()` і регулярні вирази для формату
- Honeypot-поле проти спам-ботів
- Rate limit через `$modx->cacheManager` (JSON-формат кешу, без залежності
  від opcache) проти флуду з однієї IP
- Ядро MODX не змінювалося - компонент підключається виключно через
  Snippet/Package API (`addPackage`, `createObjectContainer`), файли ядра
  не редагувались і не входять до репозиторію

## Логи
Управление → Отчёты → Журнал ошибок, префікс `[CustomForm]`

## Видалення
Видалення transport-пакета через менеджер MODX прибирає снипет, чанки і таблицю.
