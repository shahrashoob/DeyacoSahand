<?php

namespace App\Providers;

use App\Models\Utility\Menu\MenuType;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        // switch DB connection if failed

        $dbNames = ['mysql'];
        foreach ($dbNames as $dbName) {
            try {
                \DB::connection($dbName)->getPdo();
//                \Config::set( 'database.default', $dbName );

                break;
            } catch (\Exception $e) {
                die('<html>
                      <head>
                        <title>عدم اتصال به دیتابیس</title>
                      </head>
                      <body bgcolor="#afafaf" style="text-align: center; direction: rtl; padding-top: 20%;margin: auto">
                        <h1>اتصال به دیتابیس برقرار نمی باشد، لطفا با پشتیبانی تماس بگیرید.</h1>
                        <h2>' .
                    jdate(Carbon::now()->timestamp)->format(' H:i:s Y/m/d') .
                    '</h2>
                        <br/>
                        <h2>Connection: ' . $dbName . '</h2>
                        <h2>HOST: ' . env("DB_HOST") . '</h2>
                        <h2>Database: ' . env("DB_DATABASE") . '</h2>
                      </body>
                    </html>'
                );
            }
        }

        //
        Schema::defaultstringLength(191);
        if (!Schema::hasTable("menu_types")) {
            Artisan::call("migrate");
            Artisan::call("db:seed");

        }


        $menu_types = MenuType::orderByDesc("priority_number")->orderBy('id')->get();
        view()->share('menu_types', $menu_types);

        $setting = Setting::getValues();
        view()->share('setting', $setting);

        $locales = config("app.locales");
        view()->share('config_locales', $locales);

        if (isset($setting["app_debug"])) {
//            die($setting["app.debug"]);
            config(['app.debug' => $setting["app_debug"]->integer_value]);
        }

        Blade::directive('to_money', function ($number) {
            return "<?php echo number_format($number); ?>";
        });

        Blade::directive('to_persian', function ($string) {
            $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
            $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $output = str_replace($english, $persian, $string);
            return $output;
        });


        Builder::macro('whereLike', function (string $column, string $search) {
            return $this->orWhere($column, 'LIKE', '%' . $search . '%');
        });


    }
}
