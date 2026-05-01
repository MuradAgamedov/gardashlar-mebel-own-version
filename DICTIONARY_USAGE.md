# Dictionary Helper İstifadəsi

## 📚 Dictionary Strukturu

### Cədvəllər:
- **dictionaries**: `id`, `keyword`
- **dictionary_translates**: `id`, `dictionary_id`, `language_id`, `value`
- **languages**: `id`, `lang_code`, `title`, `is_main_language`

## 🔧 Helper Funksiyaları

### 1. `dict()` - Language Code ilə
```php
// Azərbaycan dilində
echo dict('welcome', 'az'); // "Xoş gəlmisiniz"

// İngilis dilində  
echo dict('welcome', 'en'); // "Welcome"

// Rus dilində
echo dict('welcome', 'ru'); // "Добро пожаловать"

// Default dil (əgər lang_code verilməsə)
echo dict('welcome'); // Main language translation
```

### 2. `dict_id()` - Language ID ilə
```php
// Language ID ilə
echo dict_id('welcome', 1); // Azərbaycan
echo dict_id('welcome', 2); // İngilis
echo dict_id('welcome', 3); // Rus

// Default (main language)
echo dict_id('welcome');
```

### 3. `dict_all()` - Bütün tərcümələr
```php
$translations = dict_all('welcome');
// Nəticə:
// [
//     'az' => 'Xoş gəlmisiniz',
//     'en' => 'Welcome', 
//     'ru' => 'Добро пожаловать'
// ]
```

## 🚀 API-də İstifadə

### ProductTypeProductController-də:
```php
public function index(Request $request, $productTypeId): JsonResponse
{
    $langCode = $request->header('Accept-Language', 'az');
    
    return response()->json([
        'success' => true,
        'message' => dict('products_loaded_successfully', $langCode),
        'data' => $products,
        'meta' => [
            'total_text' => dict('total_products', $langCode),
            'empty_text' => dict('no_products_found', $langCode)
        ]
    ]);
}
```

### Blade Template-də:
```blade
<h1>{{ dict('welcome', app()->getLocale()) }}</h1>
<p>{{ dict('description', 'az') }}</p>

@foreach($products as $product)
    <div>
        <h3>{{ $product->title }}</h3>
        <button>{{ dict('add_to_cart', 'az') }}</button>
        <button>{{ dict('add_to_favorites', 'az') }}</button>
    </div>
@endforeach
```

## 📊 Performance

### Cache:
- **Forever cache** istifadə edilir
- **Cache key format**: `dictionary_trans_{keyword}_{languageId}`
- **Language cache**: `language_code_{langCode}`

### Cache təmizləmə:
```php
use App\Helpers\DictionaryHelper;

// Konkret keyword üçün
DictionaryHelper::clearCache('welcome');

// Bütün dictionary cache-i
DictionaryHelper::clearAllCache();
```

## 🎯 Nümunə Keywords

```
welcome
login
register
logout
home
about
contact
products
categories
search
add_to_cart
add_to_favorites
view_details
price
discount
total
checkout
order_success
error_occurred
loading
save
cancel
edit
delete
confirm
```

## ⚡ Composer Autoload

Helper avtomatik yüklənir:
```bash
composer dump-autoload
```

Artıq hər yerdə `dict()`, `dict_id()`, `dict_all()` funksiyalarını istifadə edə bilərsiniz!

---

Copyright © {{ date('Y') }} Gardashlar MMC. Bütün hüquqlar qorunur.
