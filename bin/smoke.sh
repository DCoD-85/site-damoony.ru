#!/bin/bash
# bin/smoke.sh — проверка доступности ключевых URL visuo
# Запускается из cron на боевом.
# Кладёт результат в /mnt/data/logs/smoke_YYYYMMDD.log
# При ошибке — в /mnt/data/logs/alerts.log

set -uo pipefail

LOG_DIR="/mnt/data/logs"
LOG="${LOG_DIR}/smoke_$(date +%Y%m%d).log"
ALERT="${LOG_DIR}/alerts.log"
BASE="https://visuo.damoony.ru"
MAIN="https://damoony.ru"

mkdir -p "$LOG_DIR"

# Формат: "URL ОЖИДАЕМЫЙ_КОД"
CHECKS=(
    "$MAIN/ 200"
    "$BASE/ru-ru/ 200"
    "$BASE/ru-ru/news/ 200"
    "$BASE/ru-ru/news/visuo-start/ 200"
    "$BASE/ru-ru/docs/ 200"
    "$BASE/ru-ru/docs/installation/ 200"
    "$BASE/ru-ru/downloads/ 200"
    "$BASE/ru-ru/login/ 200"
    "$BASE/ru-ru/registration/ 200"
    "$BASE/en-en/ 200"
    "$BASE/ch-ch/ 200"
)

PASS=0
FAIL=0

{
    echo "=== smoke $(date '+%Y-%m-%d %H:%M:%S') ==="
    for c in "${CHECKS[@]}"; do
        url="${c% *}"
        expected="${c##* }"
        actual=$(curl -k -s -o /dev/null -w "%{http_code}" -L --max-time 10 "$url")
        if [ "$actual" = "$expected" ]; then
            echo "OK    $actual  $url"
            PASS=$((PASS+1))
        else
            echo "FAIL  $actual  $url (expected $expected)"
            FAIL=$((FAIL+1))
        fi
    done
    echo "=== PASS: $PASS  FAIL: $FAIL ==="
} | tee -a "$LOG"

if [ "$FAIL" -gt 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] SMOKE FAIL: $FAIL из $((PASS+FAIL))" >> "$ALERT"
    exit 1
fi

exit 0