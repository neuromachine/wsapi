# TASK-BE-007A — Offers Endpoint Regression Investigation

## Context

После выполнения TASK-BE-007 (Offers Endpoint Boundary Refactor) обнаружена регрессия.

### Работает

GET

/api/ru/blocks/categories/offers/internet-katalog-1

для категории

block_id = 5
category_id = 1009

возвращает корректный результат.

---

### Не работает

GET

/api/ru/blocks/categories/offers/redstar-collaboration-formats

где целевая категория имеет

block_id = 4
category_id = 5

Эндпоинт не возвращает ожидаемые данные.

До рефакторинга данный endpoint работал.

---

## Предположение

Регрессия могла появиться после TASK-BE-007.

Возможная причина:

- изменение условий выборки BlockItem;
- изменение Repository Boundary;
- изменение eager loading;
- появление фильтрации по block_id;
- изменение способа получения категории;
- изменение attach/block relationship.

Также возможно проблема появилась после коммита

8cdc25b05e37961a456afedc389aef5d77173170

Не считать данную гипотезу истинной — лишь использовать как ориентир при анализе.

---

# Цель

Не переписывать архитектуру.

Не выполнять новый рефакторинг.

Найти источник регрессии и восстановить исходное поведение endpoint.

---

# Требуется

## 1. Провести сравнительный анализ

Сравнить выполнение:

/offers/internet-katalog-1

и

/offers/redstar-collaboration-formats

на каждом этапе цепочки.

Проверить:

- поиск категории
- Repository
- eager loading
- BlockAttachMap
- Resource
- relationships
- итоговый JSON

Не ограничиваться предположением о block_id.

---

## 2. Проверить Repository

Изучить

BlockCategoryRepository::getOffersData()

и определить,

не появились ли условия вида

where(block_id = ...)

или иные ограничения,

которые раньше отсутствовали.

---

## 3. Проверить загрузку BlockItems

Определить,

получаются ли BlockItems вообще.

Если нет —

найти место,

где они теряются.

---

## 4. Проверить связи

Проверить корректность использования:

category_id

block_id

block_items

blocks

attach

relations

Не должно существовать скрытых предположений,

что Offers работают только для одного типа Block.

---

## 5. Проверить Resource

Проверить,

не исключает ли OffersResource часть данных,

которые раньше проходили напрямую.

---

## 6. Проверить влияние BE-008…BE-010

Убедиться,

что изменения

BlockCategoryResource

BlockItemResource

EavContentResolver

не затронули работу Offers.

---

## 7. Git Analysis

Если возможно,

сравнить текущую реализацию

с состоянием до

8cdc25b05e37961a456afedc389aef5d77173170

и определить,

в каком изменении возникла регрессия.

---

# Ограничения

Не менять API.

Не менять frontend.

Не менять JSON contract.

Не менять сидеры.

Не менять структуру БД.

Не выполнять дополнительный рефакторинг.

Исправление должно быть минимальным.

---

# Результат

Создать

.agents/reports/

REPORT-BE-007A-offers-endpoint-regression.md

В отчете указать:

- источник проблемы;
- почему endpoint internet-katalog-1 работает;
- почему redstar-collaboration-formats не работает;
- какие строки были изменены;
- почему исправление не влияет на остальные endpoint;
- какие endpoint дополнительно были проверены.