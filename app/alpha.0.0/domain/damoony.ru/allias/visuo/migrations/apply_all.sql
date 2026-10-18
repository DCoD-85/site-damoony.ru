-- apply_all.sql
-- Применяет все миграции по порядку.
-- Только для dev! На бою — по одной, с бэкапом.

SOURCE 001_create_news.sql;
SOURCE 002_create_files.sql;
SOURCE 003_create_file_downloads.sql;
SOURCE 004_alter_users_timezone_lang.sql;
SOURCE 005_seed_files.sql;
SOURCE 006_seed_news.sql;
SOURCE 007_create_login_attempts.sql;