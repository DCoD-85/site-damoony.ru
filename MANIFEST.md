Контекст
Что за проект: движок для мультиязычных сайтов на PHP 8.4 + MariaDB. Аллиасная архитектура (www, login, visuo — независимые модули поверх ядра). Собственный роутер с regex-маршрутами. Собственный UI-движок visuoel / visuotag.

Где работает:

Локально: E:\OSPanel543\domains\damoony2.ru\ (Windows 11, OSPanel, MariaDB 11.8.9)

GitHub: git@github.com:DCoD-85/site-damoony.ru.git (ветка main)

Боевой: visuo.damoony.ru (Debian 13, Apache 2.4, PHP 8.4.24-fpm, MariaDB 11.8.6)

Рабочая папка боевого: /var/www/damoony.ru

Бэкапы: /mnt/data/ (HDD 916 ГБ, ext4)

Схема работы
text
Локальный  →  git push  →  GitHub  →  git pull  →  Боевой
Правило №1: правки только локально. На боевом — только git pull.
Правило №2: никогда не править на боевом вручную.
Правило №3: перед правкой — git pull. После — git push.

Что закрыто (не переделывать)
Что	Где	Статус
Бэкапы БД/конфигов/файлов	/root/scripts/, cron	✅ работает
Timeshift	/mnt/data/timeshift/	✅ 5.1 ГБ, ежедневно
Clonezilla-образ SSD	/mnt/data/clonezilla/damoony-sda-20261001/	✅ 1.9 ГБ
Smoke-тесты (11 URL)	bin/smoke.sh, cron	✅ раз в час
Trash-cli	alias rm → trash-put	✅
Документация	docs/*.md, README.md, CHANGELOG.md	✅
Git	одна ветка main, .gitattributes	✅
Чистка мусора	238 файлов удалено	✅
Что осталось
Фаза 3. Аудит кода (главная задача) — см. TASK_AUDIT.md.
Фаза 4. Внешний мониторинг — UptimeRobot или Healthchecks.io.

Где искать информацию
README.md — обзор проекта, структура

docs/architecture.md — слои, роутинг, аллиасы, UI-движок

docs/deploy.md — деплой, откат, дампы, troubleshooting

docs/backup.md — бэкапы, восстановление, Clonezilla, история инцидентов

docs/development.md — правила, стиль, нюансы MariaDB на Windows

docs/AGENTS.md — прочитать первым делом (правила для агента)

CHANGELOG.md — история версий

Последний коммит
f7bac3b docs: Clonezilla — процедура создания и восстановления образа SSD