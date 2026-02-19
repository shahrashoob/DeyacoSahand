<?php

use App\Http\Controllers\PublicRelations\NotificationController;

# Loading
Route::prefix( 'notification.' )->name( "notification." )->group( function () {

    # Dashboard
    Route::get( "index", [
        NotificationController::class,
        "index"
    ] )->name( "index" );


} );
