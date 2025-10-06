<?php
    use App\Http\Controllers\Admin\AddressTypeController;
    use App\Http\Controllers\Admin\AddressController;



    Route::resource('address-types', AddressTypeController::class);
    Route::post('address-types/reorder', [AddressTypeController::class, 'reorder'])->name('address-types.reorder');

    Route::resource('addresses', AddressController::class);
    Route::post('addresses/reorder', [AddressController::class, 'reorder'])->name('addresses.reorder');
    Route::get('address-types/{addressType}/addresses/create', [AddressController::class, 'createForType'])->name('address-types.addresses.create');


