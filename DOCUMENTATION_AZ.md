**Layihə Xülasəsi**
- **Nədir:** Bu repo Laravel 12 əsaslı tam funksional veb tətbiqidir — publik REST API, ödəniş inteqrasiyaları və administrator paneli mövcuddur.
- **Texnologiyalar:** PHP ^8.2, Laravel ^12, Laravel Sanctum (API auth), l5-swagger (API docs), Maatwebsite Excel, PHPUnit.

**Tez Baxış — Fayl Strukturu**
- `app/` : Model-lər, Controller-lər, Services, Repositories və Traits.
- `routes/` : `api.php` (public və auth-required API endpointləri), `web.php` (public web routlar), `admin.php` + `routes/web/*.php` (admin panel routları).
- `resources/` : frontend JS/CSS və Blade view-lar。
- `database/` : migrations, seeders və factories。
- `tests/` : Unit və Feature testlər。

**Arxitektura və Məhz Strukturu**
- Layered approach: Controller → Service/Repository → Model。
- `app/Http/Controllers/Api` — API endpointləri。
- `app/Http/Controllers/Admin` — admin panel controller-ləri。
- `app/Services` və `app/Repositories` (mövcuddursa) biznes məntiqini izolyasiya edir。
- Middleware: `auth:sanctum` API üçün, `admin.auth` admin panel üçün。

**Tələb və Asılılıqlar**
- PHP: `^8.2`
- Composer əsas paketlər: `laravel/framework:^12`, `laravel/sanctum`, `darkaonline/l5-swagger`, `maatwebsite/excel` (bax: [composer.json](composer.json)).

**Ətraf Mühit Dəyişənləri (əsasları)**
- `APP_ENV`, `APP_KEY`, `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS`
- Ödəniş və 3rd-party: `EPAY_*`, `PAYMENT_PROVIDER_*` (layihəyə görə fərqlənə bilər)
- `QUEUE_CONNECTION` (redis/sync), `CACHE_DRIVER`, `SESSION_DRIVER`

**Quraşdırma və Lokal İstismar (Windows üçün xüsusi addımlar)**
1) PHP və Composer quraşdırın (PHP 8.2+). Path əlavə edin。

2) Repository-dən asılılıqları yükləyin:

```bash
composer install
npm install
```

3) `.env` faylını yaradın və konfiqurasiyanı doldurun:

```powershell
Copy-Item .env.example .env
```

4) Açar və DB hazırlığı:

```bash
php artisan key:generate
php artisan migrate --graceful
php artisan db:seed
```

Qeyd: kiçik layihələr üçün `DB_CONNECTION=sqlite` və `DB_DATABASE=database/database.sqlite` istifadə etmək rahatdır (faylı yaradın: `type nul > database\\database.sqlite`).

5) Lokal server və Vite (frontend):

```bash
php artisan serve
npm run dev
```

6) Queue worker və cron (istehsal üçün):

```bash
php artisan queue:work
```

**Məlumat Bazası və Model Sahələri**
- `users` cədvəlində layihədə əlavə sahələr istifadə olunur: `phone`, `pending_email`, `pending_email_token`, `pending_email_expires_at`。
- Migrasiyaları `database/migrations` qovluğunda tapmaq olar。

**API Strukturu — Xülasə və Əsas Endpointlər**
- Baza yolu: `/api` (bax: [routes/api.php](routes/api.php)).
- Public resurslar: `GET /languages`, `GET /seo-pages`, `GET /projects`, `GET /products` və s。
- Auth-required qruplar: `auth:sanctum` ilə qorumalı rout-lar (favoritlər, cart, comment, profile və s)。
- Authentication endpoints: `POST /auth/register`, `POST /login`, `POST /send-otp`, `POST /verify-otp`, `POST /forgot-password`, `POST /reset-password`。
- Ödəniş endpointləri: `POST /payment/cash`, `POST /payment/installment`, `POST /payment/epay`, `POST /payment/pay`。

**Admin Panel**
- Admin rout-ları `routes/admin.php`-də toplanır və `routes/web/*.php` faylları ilə bölünür。Admin auth middleware `admin.auth` tətbiq edilir。
- Admin login: `GET /login`, `POST /login`, `POST /logout`。

**Testlər və CI**
- Testləri işə salmaq yerli：

```bash
php artisan test
# və ya
composer test
```

- `phpunit.xml` test mühitini sqlite `:memory:` və `MAIL_MAILER=array` kimi konfiqurasiya edir。
- Tövsiyə: GitHub Actions üçün yüngül workflow nümunəsi istifadə edin (PHP 8.2, composer install, migrations, php artisan test)。Swagger yaradılması (`php artisan l5-swagger:generate`) CI-də opsionaldir。

**Kod Keyfiyyəti**
- `composer` scripts-də `laravel/pint` (PHP linter/formatter) və `pest`/`phpunit` test runner-lar mövcuddur。Lokal olaraq `vendor/bin/pint` istifadə edin。

**Swagger / API sənədləşməsi**
- `darkaonline/l5-swagger` konfiqurasiyasını yoxlayın və sənədləri yaratmaq üçün：

```bash
php artisan l5-swagger:generate
```

Swagger UI-ya baxmaq üçün layihənin konfigurasiya etdiyi `swagger` endpoint-i açın (paket sənədlərinə baxın)。

**Deploy Tövsiyələri**
- Nginx/Apache ilə `public/` kök qovluğu olaraq konfiqurasiya edin。
- `APP_ENV=production`, `APP_DEBUG=false`, və `QUEUE_CONNECTION=redis` kimi istehsal parametrlərini istifadə edin。
- Migrate və seed əmrlərini təhlükəsiz şəkildə işlədin：`php artisan migrate --force`。

**Təhlükəsizlik və Tövsiyələr**
- `.env` faylını versiya nəzarətinə qoşmayın。
- Ödəniş məlumatlarını və API açarlarını environment dəyişənlərində saxlayın。
- Rate limiting və CORS siyasətini `app/Http/Kernel.php` və `config/cors.php` vasitəsilə nəzərdən keçirin。

**Nümunə cURL sorğuları (tez-tez istifadə edilənlər)**
- Profil yeniləmə (token ilə)：

```bash
curl -X POST http://127.0.0.1:8000/api/profile/update \\
	-H "Authorization: Bearer YOUR_TOKEN" \\
	-H "Content-Type: application/json" \\
	-d '{"name":"Vəli","phone":"+994501234567"}'
```

- Email təsdiqi：

```bash
curl -X POST http://127.0.0.1:8000/api/profile/confirm-email \\
	-H "Authorization: Bearer YOUR_TOKEN" \\
	-H "Content-Type: application/json" \\
	-d '{"otp_code":"123456"}'
```

**Fayllara keçidlər**
- API routları: [routes/api.php](routes/api.php)
- Admin routları: [routes/admin.php](routes/admin.php)
- Asılılıqlar: [composer.json](composer.json)

Əlavə istəklər: istəsəniz hər bir əsas endpoint üçün nümunə sorğu/cevab (cURL), Swagger-dan tam çıxarış, və ya Windows üçün step-by-step deployment skriptini hazırlaya bilərəm。
