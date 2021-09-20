# Laravel Meditation Example App
You can run this application in Docker Container with `docker-compose up` command on your project path.

> Make sure you rename `.env.example` to `.env`. Example environment variables is ready to work on local docker environment.

> When you running application inside container, you should execute commands in the container's bash. You can connect to application bash with `docker-compose exec api bash` command.

> To install dependencies execute `make install_dependencies` or `php composer.phar install` command in `api` container's `bash`.

## Running Tests
This application is covered with e2e/feature tests, you can run tests via `php artisan test` command inside the `api` container's `bash`.

## Seeding with example data
To create meditations, you must execute `php artisan db:seed --class=MeditationSeeder`

## Endpoints

### Authentication Endpoints

#### Register Endpoint
To use this application, you should be authenticated first. If you do not have an account, you have to register from this endpoint.

| Method | PATH | Required Parameters |
| :---: | :---: | :---: |
| `POST` | /api/v1/register | email: string <br> password: string |

#### Login Endpoint
If you already have an account, you can be authenticated on this endpoint.

| Method | PATH | Required Parameters |
| :---: | :---: | :---: |
| `POST` | /api/v1/login | email: string <br> password: string |

#### Logout Endpoint
If you want to log-out from authenticated account, you can use this endpoint.

| Method | PATH |
| :---: | :---: |
| `POST` | /api/v1/logout |

### Meditation Endpoints

#### Complete Meditation
To complete meditation you can use this endpoint with authenticated user's bearer token.

| Method | PATH | Required Header |
| :---: | :---: | :---: |
| `POST` | /api/v1/meditation/complete/{meditation_id} | Authorization: Bearer <token> |

### Report Endpoints

#### General Statistics Endpoint
General statistics generates 3 different statistics between provided `startDate` and `endDate` as route parameter;

1. Total meditation count that user completed.
2. Total meditation duration that user completed.
3. The longest consecutive day count that user listens to meditation each day.

| Method | PATH | Required Parameters | Required Header |
| :---: | :---: | :---: | :---: |
| `GET` | /api/v1/meditation/report/general_statistics | startDate <br> endDate | Authorization: Bearer <token> |

#### Daily Meditation Duration Endpoint
Daily meditation duration returns that total meditation duration for each day, for statistics for a specific date range you can use date filter.

When you provide `startDate` or `endDate` or both of it, you can set total meditation duration for each day between 2 dates.

| Method | PATH | Optional Parameters | Required Header |
| :---: | :---: | :---: | :---: |
| `GET` | /api/v1/meditation/report/daily_meditation_duration | startDate <br> endDate | Authorization: Bearer <token> |

#### Monthly Meditation Days Endpoint
Monthly meditation days return a list of days that the user completed meditation in the specified month.

| Method | PATH | Required Parameters | Required Header |
| :---: | :---: | :---: | :---: |
| `GET` | /api/v1/meditation/report/active_days_in_month | month <br> year | Authorization: Bearer <token> |

