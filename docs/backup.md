# Бэкапы

## Что бэкапится

БД (visuo, login, www) — ежедневно 03:00 — `/mnt/data/db/` — хранить 14 дней.

Конфиги (`/etc/...`) — ежедневно 03:05 — `/mnt/data/configs/` — хранить 30 дней.

Файлы сайта — воскресенье 04:00 — `/mnt/data/files/` — хранить 8 недель.

Система (Timeshift) — ежедневно 03:10 — `/mnt/data/timeshift/` — 7 дней + 2 недели.

Проверка свежести — ежедневно 05:00 — `/mnt/data/logs/` — 30 дней.

**Полный образ SSD** — Clonezilla, вручную, раз в 2–3 месяца.

## Где живут скрипты

    /root/scripts/
      backup_db.sh
      backup_configs.sh
      backup_files.sh
      backup_check.sh

    /etc/cron.d/damoony-backup       расписание
    /etc/logrotate.d/damoony-backup
    /mnt/data/logs/                  логи каждого запуска

## Проверка

    # Последние логи
    ls -lt /mnt/data/logs/ | head

    # Что в бэкапах
    ls -lh /mnt/data/db/
    ls -lh /mnt/data/configs/
    ls -lh /mnt/data/files/

    # Алерты (должно быть пусто)
    cat /mnt/data/logs/alerts.log

    # Место на диске
    df -h /mnt/data

## Восстановление

### БД

    # Найти нужный дамп
    ls -lt /mnt/data/db/ | head

    # Восстановить
    gunzip -c /mnt/data/db/visuo_damoony_20260930_030001.sql.gz | \
      mysql -u root visuo_damoony

**Внимание.** Если БД не пустая — сначала удалить все таблицы, потом восстановить. Или создать новую БД.

### Файлы сайта

    # Куда распаковывать (во временную)
    mkdir -p /tmp/restore
    tar xzf /mnt/data/files/damoony_ru_20260930_040001.tar.gz -C /tmp/restore

    # Проверить содержимое
    ls -la /tmp/restore/damoony.ru/

    # Скопировать нужное поверх боевого
    cp -a /tmp/restore/damoony.ru/www/... /var/www/damoony.ru/www/...

### Конфиги

    mkdir -p /tmp/restore
    tar xzf /mnt/data/configs/configs_20260930_030501.tar.gz -C /tmp/restore
    ls -la /tmp/restore/etc/

### Система (Timeshift)

Если система загружается:

    timeshift --restore

Выбрать снапшот → 15 минут → перезагрузка.

Если не загружается — live-USB с Timeshift.

### Полный образ (Clonezilla)

Если SSD умер:

1. Новый SSD в сервер.
2. Загрузиться с Clonezilla-USB.
3. `device-image` → `restoredisk` → выбрать образ из `/mnt/data/clonezilla/`.
4. После восстановления — загрузиться.

## Мониторинг

`backup_check.sh` раз в сутки проверяет:

- свежесть дампов (не старше 25 часов)
- свежесть конфигов (не старше 25 часов)
- свежесть файлов (не старше 8 дней)
- место на `/mnt/data` (не меньше 50 ГБ)
- последний снапшот Timeshift

При ошибке — пишет в `/mnt/data/logs/alerts.log`.

**TODO.** Подключить внешние уведомления (приложение, Telegram, email).

## Ротация

- БД: старше 14 дней — удаляются
- Конфиги: старше 30 дней — удаляются
- Файлы: старше 56 дней — удаляются

**Если нужно хранить дольше** — скопировать в `/mnt/data/archive/` перед ротацией.