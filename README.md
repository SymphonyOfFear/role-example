# Rollen in Laravel

Dit is een eenvoudig project om te demonstreren hoe je rollen kunt gebruiken in Laravel. We zullen een eenvoudig results maken met twee rollen: admin en gebruiker.

## Aan de slag

1. Fork deze repository
2. Clone de repository naar je lokale machine
3. Voer `composer install` uit
4. Maak een `.env`-bestand
5. Voer `php artisan key:generate` uit
6. Voer `php artisan migrate --seed` uit
7. Voer `php artisan serve` uit
8. Voer `npm install` uit
9. Voer `npm run dev` uit
10. Bezoek `http://localhost:8000`

### ResultsController

We moeten een `ResultsController` maken. Deze controller plaatsen we in de map `Student`, die automatisch wordt aangemaakt wanneer we de controller maken.

```bash
php artisan make:controller Student/ResultsController
```

### Route

We moeten een route maken voor een resultatenoverzicht. We zullen de route in het bestand `routes/web.php` plaatsen.

```php
use App\Http\Controllers\Student\ResultsController;
//...

 Route::get('student/results', [ResultsController::class, 'index']);
```

We geven ook een naam aan de route door name-methode te gebruiken. We passen dus de code in `web.php` aan:

```php
use App\Http\Controllers\Student\ResultsController;
//...

// we voegen name('student.results') toe aan de bestaande route
Route::get('student/results', [ResultsController::class, 'index'])->name('student.results');
```

Maar we willen het resultatenoverzicht (privacy!) alleen toegankelijk maken voor geregistreerde gebruikers. We kunnen dit doen door de middleware `auth` te gebruiken. We passen dus de code opnieuw aan. Middleware `auth` zorgt ervoor dat de gebruiker is ingelogd moet zijn om de pagina te kunnen bezoeken.

```php
use App\Http\Controllers\Student\ResultsController;

//...

// we voegen middleware('auth') toe aan de bestaande route
Route::get('student/results', [ResultsController::class, 'index'])->middleware('auth')->name('student.results');
```

### Index Methode

We maken een methode `index` in de controller. Deze methode retourneert een view.

```php
public function index()
{
    return view('student.results');
}
```

### Results View

We moeten een view maken voor het results. Deze view plaatsen we in de map `student`, die automatisch wordt aangemaakt wanneer we de view maken.

```bash
php artisan make:view student/results
```

We zetten onderstaande code erin

```html
<x-app-layout>
    <x-slot name="header">
        <h2
            class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
        >
            {{ __('Resultaten overzicht') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
            >
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __('Resultaten overzicht komt hier te staan') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
```

### Navigatie aanpassen

````html
<x-nav-link
    :href="route('student.results')"
    :active="request()->routeIs('student.results')"
>
    {{ __('Resultaten overzicht') }}
</x-nav-link>
```
````

### Route Groep

We kunnen ook een route groep maken voor alle studentenroutes. We passen dus de code in `web.php` aan:

```php
use App\Http\Controllers\Student\ResultsController;

//...

Route::middleware(['auth'])->group(function () { //hier begint de groep
    Route::get('student/results', [ResultsController::class, 'index'])->name('student.results');

    // andere studentenroutes zoals student/profile, student/courses, student/grades, etc.
    Route::get('student/courses', [CourseController::class, 'index'])->name('student.courses');

});//hier eindigt de groep
```

In de bovenstaande routes herhalen we de middleware `auth` niet. We hebben de middleware `auth` toegevoegd aan de route groep. Dit betekent dat alle routes in de groep alleen toegankelijk zijn voor geregistreerde gebruikers.

#### Prefix

Dan kunnen we nog een zogenaamde prefix toevoegen aan de route groep

```php
use App\Http\Controllers\Student\ResultsController;

//...

Route::middleware(['auth'])
    ->prefix('student')//deze prefix is toegevoegd zodat alle urls beginnen met student/
    ->group(function () { //hier begint de groep
    Route::get('results', [ResultsController::class, 'index'])->name('student.results');

    // andere studentenroutes zoals profile, courses, grades, etc.
    Route::get('courses', [CourseController::class, 'index'])->name('student.courses');

});//hier eindigt de groep
```

#### Name

We kunnen ook de `namen` van van de routes toevoegen aan de groep

```php
use App\Http\Controllers\Student\ResultsController;

//...

Route::middleware(['auth'])
    ->prefix('student')//deze prefix is toegevoegd zodat alle urls beginnen met student/
    ->name('student.')//deze naam is toegevoegd zodat alle routes in de groep de naam student. krijgen
    ->group(function () { //hier begint de groep
    Route::get('results', [ResultsController::class, 'index'])->name('results');

    // andere studentenroutes zoals profile, courses, grades, etc.
    // Route::get('courses', [CourseController::class, 'index'])->name('courses');

});//hier eindigt de groep
```

### Verwijderen standaard Dashboard

We verwijderen de standaard dashboard en daarvoor moeten we eerst de navigatie aanpassen:

> Regels 5 t/m 19 verwijderen:

```html
<div class="flex">
    <!-- deze code verwijderen -->
</div>
```

> Regels 55 t/m 59 verwijderen:

```html
<div class="pt-2 pb-3 space-y-1">
    <x-responsive-nav-link
        :href="route('dashboard')"
        :active="request()->routeIs('dashboard')"
    >
        {{ __('Dashboard') }}
    </x-responsive-nav-link>
</div>
```

### Route verwijderen

We `__verwijderen__` de standaard dashboard route:

```php
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
```

## Standaard Redirect

We kunnen de standaard redirect aanpassen. In het bestand `app/Http/Controllers/Auth/AuthenticatedSessionController.php` passen we de store methode aan:

```php
class AuthenticatedSessionController extends Controller
{
    // ...

    public function store(LoginRequest $request): RedirectResponse
    {
        // ...

        $request->authenticate();

        $request->session()->regenerate();

        // return redirect()->intended(route('dashboard', absolute: false));
        return redirect()->intended(route('student.results', absolute: false));
    }
}
```

We passen ook de redirect aan bij de registratie:

```php
class RegisteredUserController extends Controller
{
    // ...

    public function store(Request $request): RedirectResponse
    {
        // ...

        // return redirect(route('dashboard', absolute: false));
        return redirect()->intended(route('student.results', absolute: false));
    }
}
```

Nu kunnen we ook de standaard dashboard view verwijderen: `resources/views/dashboard.blade.php`

## Rollen

We gaan nu rollen toevoegen. We maken een nieuw model `Role` aan en de migratie:

```bash
php artisan make:model Role -m
php artisan make:migration "add role id to users table"
```

We passen het Model Role aan:

```php
class Role extends Model
{
    protected $fillable = [
        'name',
    ];
}
```

Maar ook het Model User:

```php
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',  //deze voegen we toe
    ];

    // ...
}
```

We passen de migratie aan:

```php
//database/migrations/xxx_create_roles_table.php:
public function up()
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}

//database/migrations/xxx_add_role_id_to_users_table.php:
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')->constrained()->onDelete('cascade');
    });
}
```

Dan moeten we nog seeders maken om de rollen toe te voegen. We doen dit in de DatabaseSeeder:

```php
class DatabaseSeeder extends Seeder
{
    public function run()
    {
         User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Role::create(['name' => 'student']);
        Role::create(['name' => 'teacher']);
    }
}
```
