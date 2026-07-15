# REPORT-CONTENT-004: Tourism Vietnam CP Batch

## 1. Overview
This report summarizes the batch generation of individual commercial proposals tailored for the tourism sector in Vietnam. The files have been formatted to be seeded via `BlockForCpDataSeeder`.

## 2. Generated Proposal Keys & Output Files
The following 6 CP entries were successfully generated:
1. **`visa_run_agency`** -> `storage/app/blocks/blocks/items/ind_offers/visa_run_agency.json`
2. **`tour_desk_nha_trang`** -> `storage/app/blocks/blocks/items/ind_offers/tour_desk_nha_trang.json`
3. **`transfer_service_vietnam`** -> `storage/app/blocks/blocks/items/ind_offers/transfer_service_vietnam.json`
4. **`hotel_rental_vietnam`** -> `storage/app/blocks/blocks/items/ind_offers/hotel_rental_vietnam.json`
5. **`beauty_services_tourists`** -> `storage/app/blocks/blocks/items/ind_offers/beauty_services_tourists.json`
6. **`medical_tourism_clinic`** -> `storage/app/blocks/blocks/items/ind_offers/medical_tourism_clinic.json`

## 3. Language Coverage
- **Russian (`ru`)**: Fully generated (Root `properties`).
- **English (`en`)**: Fully generated (`en.properties`).
- **Vietnamese (`vi`)**: Placeholder populated (`vi.properties: {}`) per contract.

## 4. Pricing Assumptions
Prices were assumed based on standard Southeast Asia / Vietnam B2B digital service rates:
- **Entry Package**: ~$1,000 - $1,500 (Basic sites, standard catalogs, WhatsApp forms).
- **Growth / Business Package**: ~$2,000 - $3,500 (CRM integrations, online booking, seat limits, iCal syncs).
- **System / Premium Package**: ~$4,000 - $7,000 (Telegram bots, driver/staff apps, PMS integrations, multi-language ecosystems).

## 5. Unresolved Human Decisions
- **Pricing Verification**: The business team must review and adjust the dollar amounts in the generated CP files before final publication.
- **Service Inclusions**: Features (e.g., specific CRMs, payment gateways like Stripe vs. local VN gateways) should be verified for technical feasibility.

## 6. Next Steps
Run the seeder to test these proposals in the database:
```bash
php artisan db:seed --class=BlockForCpDataSeeder
```
Then verify via API: `GET /api/en/blocks/categories/offers/{proposalKey}`
