# IND-OFFERS-TOURISM-CP-BATCH-CONTRACT

## Scope

This contract applies to individual commercial proposal JSON files:

```text
storage/app/blocks/blocks/items/ind_offers/{proposalKey}.json
```

They are consumed by:

```text
database/seeders/BlockForCpDataSeeder.php
```

## Canonical properties

Current canonical properties for `ind_offers`:

```text
title
content
acticle
items
hero
benefits
includes
reelsSystem
extras
important
```

Do not use unsupported fields such as `final` unless a separate backend/schema task adds them.

Use `acticle` for the final persuasive closing / summary when needed.

## Locale model

```text
ru -> root properties
en -> en.properties
vi -> vi.properties
```

For a tourism/Vietnam batch, English should normally be filled. Russian may be filled when the target audience is Russian-speaking. Vietnamese can remain placeholder unless specifically requested.

## Required CP sections

A strong CP should usually contain:

```text
hero
benefits
extras
important
items
includes
acticle
```

## Package rules

The `items` section should contain 3 packages:

```text
Basic / Entry
Growth / Business
System / Premium
```

Each package should include:

```text
index
icon
name
price
term
featured
desc
features
```

## Tourism/Vietnam batch examples

Potential entries:

```text
visa-run agency
tour desk
transfer service
hotel / apartment rental
beauty services for tourists
medical tourism clinic
```
