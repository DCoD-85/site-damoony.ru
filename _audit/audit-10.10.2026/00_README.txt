================================================================
AUDIT — 2026-10-10
Срез структуры сайта damoony2.ru (alpha.0.0)
================================================================

ЧТО ЭТО
  Аудит-срез проекта на дату 10.10.2026, после серии
  рефакторингов (fillers, subalgoritm, visuoelement legacy).

СОДЕРЖИМОЕ
  00_README.txt       — этот файл (оглавление)
  01_architecture.txt — слои, связи, поток исполнения
  02_sources.txt      — модули sources/ (без visuo/)
  03_domain.txt       — домен damoony.ru (все подпапки)
  04_visuo_engine.txt — движок visuo (visuotag, visuoel, ...)
  05_history.txt      — git log с контекстом
  06_state.txt        — текущее состояние + проблемы
  07_todo.txt         — открытые задачи
  prodoc/             — 46 файлов полной документации (см. ниже)

PRODOC/ (46 файлов)
  01–09   корневые файлы домена (index, main, domain, allias, ...)
  10–17   alliasglobal (block, menu, scripts, все блоки)
  18–26   visuo (include, algoritm, helpers, user, lang, dataprovider, scheme)
  30–36   sources без visuo (include, core, db, services, library, render, gmod)
  37–47   движок visuo (visuotag, visuoattrs, visuoel, visuostruct, ...)

КАК ЧИТАТЬ
  1. Начни с 01_architecture.txt — общая карта.
  2. 06_state.txt — что сделано, что сломано, что можно удалить.
  3. 07_todo.txt — что делать дальше.
  4. prodoc/* — детали по каждому слою.