<?php
use \Illuminate\Support\Facades\Route;

Route::group(['prefix'=>config('car.car_route_prefix')],function(){
    Route::get('/pickup-locations','CarController@pickupLocations')->name('car.pickup_locations');
    Route::get('/transfer-locations','CarController@transferLocations')->name('car.transfer_locations');
    Route::get('/transfer-availability/{car}','CarController@availability')->name('car.transfer.availability');
    Route::post('/transfer-quote/{car}','CarController@quote')->name('car.transfer.quote');
    Route::get('/','CarController@index')->name('car.search'); // Search
    Route::get('/{slug}','CarController@detail')->name('car.detail');// Detail
});

Route::group(['prefix'=>'user/'.config('car.car_route_prefix'),'middleware' => ['auth','verified']],function(){
    Route::get('/','ManageCarController@manageCar')->name('car.vendor.index');
    Route::get('/create','ManageCarController@createCar')->name('car.vendor.create');
    Route::get('/edit/{id}','ManageCarController@editCar')->name('car.vendor.edit');
    Route::get('/del/{id}','ManageCarController@deleteCar')->name('car.vendor.delete');
    Route::post('/store/{id}','ManageCarController@store')->name('car.vendor.store');
    Route::get('bulkEdit/{id}','ManageCarController@bulkEditCar')->name("car.vendor.bulk_edit");
    Route::get('/booking-report/bulkEdit/{id}','ManageCarController@bookingReportBulkEdit')->name("car.vendor.booking_report.bulk_edit");
    Route::get('/recovery','ManageCarController@recovery')->name('car.vendor.recovery');
    Route::get('/restore/{id}','ManageCarController@restore')->name('car.vendor.restore');

    Route::group(['prefix' => 'transfer-locations'], function () {
        Route::get('/', 'Vendor\\TransferLocationController@index')->name('car.vendor.transfer-locations.index');
        Route::get('/create', 'Vendor\\TransferLocationController@create')->name('car.vendor.transfer-locations.create');
        Route::get('/edit/{id}', 'Vendor\\TransferLocationController@edit')->name('car.vendor.transfer-locations.edit');
        Route::post('/store/{id?}', 'Vendor\\TransferLocationController@store')->name('car.vendor.transfer-locations.store');
        Route::delete('/{id}', 'Vendor\\TransferLocationController@destroy')->name('car.vendor.transfer-locations.destroy');
    });

    Route::group(['prefix' => 'transfer-service-centers'], function () {
        Route::get('/', 'Vendor\\TransferServiceCenterController@index')->name('car.vendor.transfer-service-centers.index');
        Route::get('/create', 'Vendor\\TransferServiceCenterController@create')->name('car.vendor.transfer-service-centers.create');
        Route::get('/edit/{id}', 'Vendor\\TransferServiceCenterController@edit')->name('car.vendor.transfer-service-centers.edit');
        Route::post('/store/{id?}', 'Vendor\\TransferServiceCenterController@store')->name('car.vendor.transfer-service-centers.store');
        Route::delete('/{id}', 'Vendor\\TransferServiceCenterController@destroy')->name('car.vendor.transfer-service-centers.destroy');
    });
});

Route::group(['prefix'=>'user/'.config('car.car_route_prefix')],function(){
    Route::group(['prefix'=>'availability'],function(){
        Route::get('/','AvailabilityController@index')->name('car.vendor.availability.index');
        Route::get('/loadDates','AvailabilityController@loadDates')->name('car.vendor.availability.loadDates');
        Route::post('/store','AvailabilityController@store')->name('car.vendor.availability.store');
    });
});
