<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Online Bali Kami Tour'),

    'version' => env('APP_VERSION', '2.0.1'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),
    'app_url' =>env('APP_URL', "https://online.balikamitour.com"),
    'app_status' =>env('APP_STATUS', "Online"),
    'app_version' => env('APP_VERSION', "5.6"),
    'app_key' => env('APP_KEY', "base64:xg2GjTsNwRh59HUV11ZdrzHnVY+esAW0wkKw1xAWQhk="),
    'exchange_rate_api_key' => env('EXCHANGE_RATE_API_KEY', "6bf13447595262dab3f2d6c1"),

    'alt_logo' => env('ALT_LOGO', "Logo Bali Kami Group"),
    'logo_dark' => env('LOGO_DARK', "storage/logo/logo-color-bali-kami.png"),
    'logo_img_color' => env('LOGO_IMG_COLOR', "logo-color-bali-kami.png"),
    'logo_img_white' => env('LOGO_IMG_WHITE', "logo-white-bali-kami.png"),
    'logo_img_black' => env('LOGO_IMG_BLACK', "logo-black-bali-kami.png"),
    
    // 'reservation_mail' => env('RESERVATION_MAIL', "reservation@balikamitour.com"), // online
    'reservation_mail' => env('RESERVATION_MAIL', "e-admin@balikamitour.com"), // offline
    'administrator_mail' => env('ADMINISTRATOR_MAIL', "e-admin@balikamitour.com"),
    
    'wa_me' => env('WA_ME', "https://wa.me/09999999999/?"),
    'business' => env('BUSINESS', "Bali Kami Tour & Wedding"),
    'vendor' => env('VENDOR', "Tourism Service Information System"),
    'term' => env('TERM', "terms-and-conditions"),
    'privacy' => env('PRIVACY', "privacy-policy"),
    'user_manual' => env('USER_MANUAL', "user-manual"),
    'help' => env('HELP', "help"),
    'contact' => env('CONTACT', "contact"),
    'developer' => env('DEVELOPER', "Eka Koel"),
    'locale' => env('LOCALE', "en"),

    'bali_contact_name_01'=> env('BALI_CONTACT_NAME_01',"Admin"),
    'bali_contact_phone_01'=> env('BALI_CONTACT_PHONE_01',"+62-361-710-661"),
    'bali_contact_name_02'=> env('BALI_CONTACT_NAME_02',"Maggie"),
    'bali_contact_phone_02'=> env('BALI_CONTACT_PHONE_02',"+62-8123-811-823"),
    'bali_contact_name_03'=> env('BALI_CONTACT_NAME_03',"Charlie"),
    'bali_contact_phone_03'=> env('BALI_CONTACT_PHONE_03',"+62-811-380-967"),
    'bali_contact_office'=> env('BALI_CONTACT_OFFICE',"Bali Kami Tour & Wedding"),
    'bali_contact_office_phone'=> env('BALI_CONTACT_OFFICE_PHONE',"+62-361-710661 / 710663 / 710664"),


    'bali_contact_person_name_i'=> env('BALI_CONTACT_PERSON_NAME_I',"Ellise"),
    'bali_contact_person_phone_i'=> env('BALI_CONTACT_PERSON_PHONE_I',"+62 821-1558-8558"),
    'bali_contact_person_name_ii'=> env('BALI_CONTACT_PERSON_NAME_II',"Magie"),
    'bali_contact_person_phone_ii'=> env('BALI_CONTACT_PERSON_PHONE_II',"+62 812-381-1823"),
    'company_phone_number_i'=> env('COMPANY_PHONE_NUMBER_I',"+62 361-710-661"),
    'company_phone_number_ii'=> env('COMPANY_PHONE_NUMBER_II',"+62 361-710-663"),
    'company_phone_number_iii'=> env('COMPANY_PHONE_NUMBER_III',"+62 361-710-664"),
    'emergency_contact_name_i'=> env('EMERGENCY_CONTACT_NAME_I',"Mr. Charlie Chen"),
    'emergency_contact_phone_i'=> env('EMERGENCY_CONTACT_PHONE_I',"+62 811-380-967"),

    'DOKU_CLIENT_ID' => env('DOKU_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'Asia/Singapore',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    // 'locale' => env('LOCALE'),
    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    // 'fallback_locale' => env('LOCALE'),
    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => env('LOCALE'),
    // 'faker_locale' => 'id_ID',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */
        RealRashid\SweetAlert\SweetAlertServiceProvider::class,
        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
        App\Providers\ProductRepositoryServiceProvider::class,
        Barryvdh\DomPDF\ServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Alert' => RealRashid\SweetAlert\Facades\Alert::class,
        'PDF' => Barryvdh\DomPDF\Facade::class,
        'OrderLog' => App\Helpers\OrderLog::class,
    ],

];
