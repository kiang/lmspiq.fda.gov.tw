# Taiwan Drug Licenses Archive

This project archives drug license data from Taiwan Food and Drug Administration's License Management System ([藥物許可證查詢作業平台](https://lmspiq.fda.gov.tw/web/)).

## About

The system regularly collects and archives drug license information from Taiwan FDA's official platform. The archived data is accessible through a user-friendly interface at [drugs.olc.tw](https://drugs.olc.tw/).

This data includes:
- Drug license details
- Historical changes of licenses
- Basic product information

## Data Structure

The archived data is stored in JSON format under the `raw/licenses/` directory. Each license file contains:
- License ID (licId)
- License base ID (licBaseId)
- Product details
- Historical changes (if any)

## Scripts

- `scripts/02_full_refresh.php`: Updates license data that hasn't been refreshed in the past 3 days

## Data Source

All data is sourced from the official Taiwan FDA License Management System:
https://lmspiq.fda.gov.tw/web/

## License

- Scripts and code are released under the [MIT License](LICENSE)
- Data is available under [Creative Commons Attribution License (CC-BY)](https://creativecommons.org/licenses/by/4.0/). The original data source is Taiwan Food and Drug Administration.
