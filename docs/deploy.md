# Деплой

## Схема

    Локальный (Windows)  →  commit + push  →  GitHub
                                                  ↓
    Боевой (Debian)      →  git pull       →  применить

**Правило.** Все правки — только локально. На боевом — только `git pull`.

## Шаг 1. Локально

    cd E:\OSPanel543\domains\damoony2.ru

    git status
    git add <файлы>
    git commit -m "описание изменения"
    git push origin main

## Шаг 2. Боевой

    cd /var/www/damoony.ru

    git pull origin main

    # Если менялись PHP-классы — сбросить OPcache
    systemctl reload php8.4-fpm

    # Если менялись миграции БД — применить вручную
    mysql -u root visuo_damoony < www/logic/alpha.0.0/domain/damoony.ru/allias/visuo/migrations/XXX_new.sql

## Проверка

    # 5 URL должны отвечать 200
    for u in \
      "https://visuo.damoony.ru/ru-ru/" \
      "https://visuo.damoony.ru/ru-ru/news/" \
      "https://visuo.damoony.ru/ru-ru/docs/" \
      "https://visuo.damoony.ru/ru-ru/login/" \
      "https://visuo.damoony.ru/ru-ru/downloads/"
    do
      CODE=$(curl -k -s -o /dev/null -w "%{http_code}" -L "$u")
      echo "$CODE  $u"
    done

## Откат

**Если что-то сломалось:**

    # Посмотреть историю
    git log --oneline -10

    # Откатиться на предыдущий коммит
    git reset --hard HEAD~1

    # Или на конкретный
    git reset --hard <commit-hash>

    # Сбросить OPcache
    systemctl reload php8.4-fpm

**Если правилась БД:** восстановить из бэкапа (см. `docs/backup.md`).

## Срочный хотфикс

Если править нужно немедленно:

1. Правишь локально.
2. Коммит + push.
3. На боевом: `git pull`.
4. Если правил на боевом напрямую — **обязательно** перенести правку в локальный и закоммитить.

Правильный путь — не нарушать схему.

## Troubleshooting

**500 на всех страницах.** Ошибка в PHP. Смотреть:

    tail -50 /var/log/apache2/damoony.ru-error.log

**404 на visuo.damoony.ru.** Проблема с vhost или DNS:

    apachectl -S | grep visuo

**Редирект по кругу.** Проблема с кукой `lang`. DevTools → Cookies.

**Белый экран.** OPcache не сбросился:

    systemctl reload php8.4-fpm

**Access denied for user damoony.** Нет прав на БД:

    mariadb -u root -e "GRANT ALL PRIVILEGES ON visuo_damoony.* TO 'damoony'@'localhost'; FLUSH PRIVILEGES;"