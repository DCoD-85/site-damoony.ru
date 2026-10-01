Правила работы для агента. Прочитать первым делом.

Миссия
Ты работаешь с проектом damoony.ru — движок для мультиязычных сайтов на PHP 8.4 + MariaDB. Твоя задача — помогать развивать проект, не ломая работающее.

Первое, что нужно прочитать
docs/AGENTS.md (этот файл) — правила работы

MANIFEST.md — где что лежит, что сделано, что осталось

docs/architecture.md — как устроен движок

docs/development.md — стиль, нюансы

Как работать
Схема:

text
Локальный  →  commit + push  →  GitHub  →  git pull  →  Боевой
Строгие правила:

Никогда не править на боевом вручную. Только git pull.

Все правки — локально. Тестировать локально, потом push.

Перед правкой — git pull. После — git push.

Один коммит = одна фича. Не смешивать.

Не коммитить: secure/db.config, .env, cache/, логи, .zip-архивы > 5 МБ.

Не использовать git add . — только конкретные файлы.

Не использовать git push --force — никогда.

Деплой (кратко)
Локально:

powershell
cd E:\OSPanel543\domains\damoony2.ru
git add <файлы>
git commit -m "описание"
git push origin main
Боевой:

bash
cd /var/www/damoony.ru
git pull origin main
systemctl reload php8.4-fpm
bash /var/www/damoony.ru/bin/smoke.sh
Стиль кода
Отступы: табы

Пробелы в скобках: function foo ( ) {

Пробелы вокруг операторов: $a = $b + $c ;

Namespace аллиасов: damoony\{name}

Namespace ядра: глобальный (main, db, language)

Безопасность (обязательно)
CSRF: все POST-формы через csrf_check()

Экранирование: пользовательский вывод через h()

SQL: только через placeholder %s / %d

Пароли: только password_hash() / password_verify()

Path traversal: basename() + realpath() + str_starts_with()

Нюансы окружения
Локально (Windows):

MariaDB 11.8.9, запускается через OSPanel

Подключение: -h 127.0.0.1 (не localhost — IPv6 проблема)

Флаг --skip-ssl обязателен

Дамп: --result-file=, не > (PowerShell ломает кодировку)

Боевой (Debian):

PHP-FPM 8.4.24, MariaDB 11.8.6

После правки класса — systemctl reload php8.4-fpm

Бэкапы: cron, /mnt/data/, логи в /mnt/data/logs/

Грабли, на которые не наступать
rm на боевом — заменён на trash-put. Файлы идут в корзину, не удаляются.

GUI-файловый менеджер на боевом — не использовать. Только терминал.

PowerShell > — ломает UTF-16. Только --result-file=.

MariaDB 11.8 — убрала Etc/GMT-3. Использовать +03:00.

localhost в MariaDB на Windows — резолвится в IPv6, использовать 127.0.0.1.

Клиент MariaDB 11.8 требует SSL — использовать --skip-ssl если сервер без SSL.

--skip-grant-tables — блокирует CREATE USER/GRANT. Только для восстановления пароля.

Инциденты (прочитать, чтобы не повторить)
Инцидент 1. GUI Thunar унёс /var/www/* в корзину. Спасли из /root/.local/share/Trash/. /var/run стал реальным каталогом — Apache/MariaDB не стартовали. Симлинк восстановили.

Инцидент 2. MariaDB 11.8 убрала Etc/GMT-3. Сервер падал при старте. Заменили на +03:00.

Инцидент 3. PowerShell > с mysqldump записал UTF-16. Импорт не работал. Решение: --result-file=.

Подробно: docs/backup.md → раздел «История инцидентов».

Что уже сделано (не переделывать)
Бэкапы: cron, 4 скрипта, logrotate

Timeshift: ежедневно

Clonezilla: образ /mnt/data/clonezilla/damoony-sda-20261001/

Smoke-тесты: bin/smoke.sh, cron раз в час

Trash-cli

Документация: README.md, docs/*.md

Git: одна ветка main, .gitattributes

Что осталось (Фаза 3, 4)
Фаза 3. Аудит кода — TASK_AUDIT.md. Полный разбор структуры, мёртвый код, N+1, безопасность, БД.

Фаза 4. Внешний мониторинг — UptimeRobot или Healthchecks.io.

Полезные команды
Проверка сервисов:

bash
systemctl is-active apache2 php8.4-fpm mariadb cron
Проверка сайта:

bash
curl -k -s -o /dev/null -w "%{http_code}\n" -L https://visuo.damoony.ru/ru-ru/news/
Логи:

bash
tail -50 /var/log/apache2/damoony.ru-error.log
tail -50 /mnt/data/logs/alerts.log
Бэкапы:

bash
ls -lt /mnt/data/db/ | head
ls -lt /mnt/data/configs/ | head
cat /mnt/data/logs/alerts.log
Откат:

bash
cd /var/www/damoony.ru
git log --oneline -10
git reset --hard HEAD~1
systemctl reload php8.4-fpm
Принципы
Не ломать работающее. Лучше оставить как есть, чем «улучшить» и сломать.

Сначала понять, потом менять. Прочитать код, найти аналоги, только потом править.

Тестировать локально. Деплой — только после успешного локального теста.

Документировать решения. Если что-то сделал нестандартно — записать в docs.

Не оптимизировать преждевременно. Работает — не трогай.

Не гадать. Смотреть логи, код, документацию. Если непонятно — спросить.

Контакты
Владелец проекта: Константин В. (dcod@rambler.ru)

GitHub: DCoD-85/site-damoony.ru

Боевой сервер: SSH под root