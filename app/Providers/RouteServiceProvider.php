<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
     const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot() {
        $this->configureRateLimiting();

        $this->routes( function () {
            Route::prefix( 'api' )
                 ->middleware( 'api' )
                 ->name( "api." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/api.php' ) );

            Route::middleware(['api', 'auth:sanctum', 'verified'])
                ->prefix('api/software_system/deyaco/product')
                ->namespace($this->namespace)
                ->group(base_path('routes/api/software_system/deyaco/product.php'));

            Route::middleware(['api', 'auth:sanctum', 'verified'])
                ->prefix('api/software_system/deyaco/order')
                ->namespace($this->namespace)
                ->group(base_path('routes/api/software_system/deyaco/order.php'));

            Route::middleware( 'web' )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/web.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( "lkj/sfi/e/n/dxfdsf/sdfsd/s/dfl/import" )
                 ->name( "import." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/import.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( "lkj/sfi/e/n/dxfdsf/sdfsd/s/dfl/utility" )
                 ->name( "utility." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/utility.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( "aie/slja/ie/ij/h/als/jk/hr" )
                 ->name( "hr." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/hr.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( "aie/slja/wertuyi/als/jk/hr" )
                 ->name( "line_product_station." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/line_product_station.php' ) );


            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( "a46/ldj/lasdfsefie/dashboard" )
                 ->name( "production." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/production.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( "asasl/iej/slie/group_customer" )
                 ->name( "customer_group." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/customer_group.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( "sdfs/iesdfj/ssflie/iwh" )
                 ->name( "wh." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/wh.php' ) );


            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'aeskf/uei/weuyrw/ieus/khfu/sales' )
                 ->name( "sales." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/sales.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'auy/dfrddfg/ous/packing_form' )
                 ->name( "packing_form." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/packing_form.php' ) );


            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ajew/ioruwoe/wo/ejsd/lfie/report' )->
                name( "report." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/report.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ajew/asdf/sfesdf/dsfee/accounting' )->
                name( "accounting." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/accounting.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'asdf/wr/wrfee/packing' )->
                name( "packing." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/packing.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'asds/sdfswr/dffesf/public_relations/' )->
                name( "public_relations." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/public_relations.php' ) );


            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'asdf/pif/swerdf/siweaqn/contractor/' )->
                name( "contractor." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/contractor.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'asiwe/samsif/di/eniln/supplier/' )->
                name( "supplier." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/supplier.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'wrls/kifsd/wetor/guarding' )->
                name( "guarding." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/guarding.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'esdfsd/lsjie/kitor/quality_control' )->
                name( "quality_control." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/quality_control.php' ) );

            // Goods Kind Process Routes

//            Fabric Raw
            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ajew/asdf/goods_kind_process/fabric_raw' )->
                name( "fabric_raw." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/fabric_raw/fabric_raw.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ajew/asdf/goods_kind_process/fabric_raw/jacquard/' )->
                name( "fabric_raw.jacquard." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/fabric_raw/jacquard/jacquard.php' ) );

//            Warps
            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'erty/poi/goods_kind_process/warps' )->
                name( "warps." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/warps/warps.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ajew/asdf/goods_kind_process/warps/matthys/' )->
                name( "warps.matthys." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/warps/matthys/matthys.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'asdfd/sdf/goods_kind_process/warps/karl_mayer/' )->
                name( "warps.karl_mayer." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/warps/karl_mayer/karl_mayer.php' ) );

//            Fabric
            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ghyuji/oi/goods_kind_process/fabric' )->
                name( "fabric." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/fabric/fabric.php' ) );

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'poia/edt/sedf/goods_kind_process/fabric/special_production/' )->
                name( "fabric.special_production." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/fabric/special_production/special_production.php' ) );

            Route::middleware(['web', 'auth:sanctum', 'verified'])
                ->prefix('pse/lod/qasf/goods_kind_process/fabric/finishing_machine/')->
                name("fabric.finishing_machine.")
                ->namespace($this->namespace)
                ->group(base_path('routes/goods_kind_process/fabric/finishing_machine/finishing_machine.php'));

            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ajew/asdf/goods_kind_process/fabric_raw/dobby/' )->
                name( "fabric_raw.dobby." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/fabric_raw/dobby/dobby.php' ) );


            Route::middleware( [ 'web', 'auth:sanctum', 'verified' ] )
                 ->prefix( 'ajew/wetddf/goods_kind_process/yarn' )->
                name( "yarn." )
                 ->namespace( $this->namespace )
                 ->group( base_path( 'routes/goods_kind_process/yarn.php' ) );



        } );



    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting() {
        RateLimiter::for( 'api', function ( Request $request ) {
            return Limit::perMinute( 300 )->by( optional( $request->user() )->id ?: $request->ip() );
        } );
    }

}





//geoip_db_get_all_info()dfg
//dfg
//and gnupg_geterror(g)
