<?php
use \Illuminate\Support\Facades\Route;
Route::get('/','CarController@index')->name('car.admin.index');
Route::get('/create','CarController@create')->name('car.admin.create');
Route::get('/edit/{id}','CarController@edit')->name('car.admin.edit');
Route::post('/store/{id}','CarController@store')->name('car.admin.store');
Route::post('/bulkEdit','CarController@bulkEdit')->name('car.admin.bulkEdit');
Route::get('/recovery','CarController@recovery')->name('car.admin.recovery');
Route::get('/getForSelect2','CarController@getForSelect2')->name('car.admin.getForSelect2');

Route::group(['prefix'=>'attribute'],function (){
    Route::get('/','AttributeController@index')->name('car.admin.attribute.index');
    Route::get('/edit/{id}','AttributeController@edit')->name('car.admin.attribute.edit');
    Route::post('/store/{id}','AttributeController@store')->name('car.admin.attribute.store');
    Route::post('/editAttrBulk','AttributeController@editAttrBulk')->name('car.admin.attribute.editAttrBulk');

    Route::get('/terms/{id}','AttributeController@terms')->name('car.admin.attribute.term.index');
    Route::get('/term_edit/{id}','AttributeController@term_edit')->name('car.admin.attribute.term.edit');
    Route::post('/term_store','AttributeController@term_store')->name('car.admin.attribute.term.store');
    Route::post('/editTermBulk','AttributeController@editTermBulk')->name('car.admin.attribute.term.editTermBulk');

    Route::get('/getForSelect2','AttributeController@getForSelect2')->name('car.admin.attribute.term.getForSelect2');
});

Route::group(['prefix'=>'availability'],function(){
    Route::get('/','AvailabilityController@index')->name('car.admin.availability.index');
    Route::get('/loadDates','AvailabilityController@loadDates')->name('car.admin.availability.loadDates');
    Route::post('/store','AvailabilityController@store')->name('car.admin.availability.store');
    Route::post('/settings/{car}','AvailabilityController@updateSettings')->name('car.admin.availability.updateSettings');
});

Route::group(['prefix' => 'transfer-locations'], function () {
    Route::get('/', 'TransferLocationController@index')->name('car.admin.transfer-locations.index');
    Route::get('/create', 'TransferLocationController@create')->name('car.admin.transfer-locations.create');
    Route::get('/edit/{id}', 'TransferLocationController@edit')->name('car.admin.transfer-locations.edit');
    Route::post('/store/{id?}', 'TransferLocationController@store')->name('car.admin.transfer-locations.store');
    Route::delete('/{id}', 'TransferLocationController@destroy')->name('car.admin.transfer-locations.destroy');
});

Route::group(['prefix' => 'transfer-service-centers'], function () {
    Route::get('/', 'TransferServiceCenterController@index')->name('car.admin.transfer-service-centers.index');
    Route::get('/create', 'TransferServiceCenterController@create')->name('car.admin.transfer-service-centers.create');
    Route::get('/edit/{id}', 'TransferServiceCenterController@edit')->name('car.admin.transfer-service-centers.edit');
    Route::post('/store/{id?}', 'TransferServiceCenterController@store')->name('car.admin.transfer-service-centers.store');
    Route::delete('/{id}', 'TransferServiceCenterController@destroy')->name('car.admin.transfer-service-centers.destroy');
});

