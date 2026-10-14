<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        if(Auth::user()->type == 1){
            return redirect()->route('simulationcreate');
        }else{
            return redirect()->route('private');
        }
        return redirect()->route('simulationcreate');
    }else{
        return view('welcome');
    }
});

Auth::routes();

Route::group(['middleware' => ['admin']], function () {
    Route::get('/logout', 'HomeController@logout')->name('logout');
    Route::get('/document', 'HomeController@document')->name('document');
    Route::get('/simulationlist', 'SimulationController@create')->name('simulationlist');
    Route::get('/simulationcreate', 'SimulationController@index')->name('simulationcreate');
    Route::get('/simulation_history', 'SimulationController@simulation_history')->name('simulation_history');
    Route::get('/simulation_history/report/{id}', 'SimulationController@simulation_history_report_download')->name('simulation_history_report_download');
    Route::get('/simulation_history/report-status/{id}', 'SimulationController@simulation_history_report_status')->name('simulation_history_report_status');
    Route::post('/simulation_history_download', 'SimulationController@simulation_history_download')->name('simulation_history_download');
    Route::post('/simulation_history_delete', 'SimulationController@simulation_history_delete')->name('simulation_history_delete');
    Route::post('/regeneratepin', 'SimulationController@regeneratepin')->name('regeneratepin');
    Route::get('/get_simulation', 'SimulationController@get_simulation')->name('get_simulation');
    Route::get('/token_creation/{id}', 'SimulationController@token_creation')->name('token_creation');
    Route::get('/print_token/{id}', 'SimulationController@print_token')->name('print_token');
    Route::post('/nameupdate', 'SimulationController@nameupdate')->name('/nameupdate');
    Route::post('/simulationnamecheck', 'SimulationController@simulationnamecheck')->name('/simulationnamecheck');
    Route::post('/updatesimulationnamecheck', 'SimulationController@updatesimulationnamecheck')->name('/updatesimulationnamecheck');
    Route::post('/simulationsave', 'SimulationController@store')->name('simulationsave');
    Route::post('/simulationupdate', 'SimulationController@update')->name('simulationupdate');
    Route::get('/simulationedit/{id}', 'SimulationController@edit')->name('simulationedit');
    Route::get('/settings', 'SettingsController@index')->name('/settings');
    Route::post('/saveipaddress', 'SettingsController@saveipaddress')->name('/saveipaddress');
    Route::post('/pushercre', 'SettingsController@pushercre')->name('/pushercre');
    Route::post('/handlezip', 'SettingsController@handlezip')->name('/handlezip');
    Route::post('/resetipaddress', 'SettingsController@resetipaddress')->name('/resetipaddress');
    Route::get('/links', 'UploadsController@links')->name('links');
    Route::post('/links', 'UploadsController@addLink')->name('add_link');
    Route::post('/fileupload_store', 'UploadsController@fileupload_store')->name('fileupload_store');
    Route::get("file_delete/{id}", 'UploadsController@delete')->name("file_delete");
    Route::get("link_delete/{id}", 'UploadsController@deleteLink')->name("link_delete");
    Route::post('/linkupdate', 'UploadsController@linkupdate')->name('/linkupdate');
    Route::post('/imageupload_store', 'UploadsController@imageupload_store')->name('imageupload_store');
    Route::post('video/store', ['as'=>'video.store','uses'=>'UploadsController@store']);
    Route::post('/upload/storevideo', 'UploadsController@storevideo')->name('/upload/storevideo');
    Route::get('/upload/storeimagepage', 'UploadsController@index')->name('/upload/storeimagepage');
    Route::get('/image_upload', 'UploadsController@imageupload_index')->name('image_upload');
    Route::get('/file_upload', 'UploadsController@fileupload_index')->name('file_upload');
    Route::get('/videoupload', 'UploadsController@index')->name('/videoupload');
    Route::post('/videosearch', 'SearchController@videosearch')->name('/videosearch');
    Route::post('/imagesearch', 'SearchController@imagesearch')->name('/imagesearch');
    Route::post('/documentsearch', 'SearchController@documentsearch')->name('/documentsearch');
    Route::post('/featuredvideo/search', 'SearchController@featuredvideo')->name('/featuredvideo');
    Route::post('/featureddoc/search', 'SearchController@featureddoc')->name('/featureddoc');
    Route::post('/featuredimg/search', 'SearchController@featuredimg')->name('/featuredimg');
 });

// Route::get('/home', 'HomeController@index')->name('/home');
Route::get('/home', 'SimulationController@index')->name('/home');
Route::get('/private', 'HomeController@private')->name('private');
Route::get('/users', 'HomeController@users')->name('users');
Route::get('/sess', 'SimulationController@sess')->name('sess');
Route::get('messages', 'MessageController@fetchMessages');
Route::post('messages', 'MessageController@sendMessage');
Route::get('/private-messages/{user}/{simulationId?}', 'MessageController@privateMessages')->name('privateMessages');
Route::post('/private-messages/{user}/{simulationId?}', 'MessageController@sendPrivateMessage')->name('privateMessages.store');
Route::resource('/program', 'process\ProgramController');
Route::get('/start_chat/{id}', 'HomeController@start_chat')->name('start_chat');
Route::get('/loginuser/{id?}', 'SimulationController@loginuser')->name('loginuser');
Route::post('/loginauth', 'SimulationController@loginauth')->name('loginauth');
Route::post('/delivery_status/{id}/{status}', 'SimulationController@delivery_status')->name('delivery_status');
Route::get('/delivery_status_click/{userid}', 'SimulationController@delivery_status_click')->name('delivery_status_click');
Route::get('/existinguserlogin', 'SimulationController@existinguserlogin')->name('/existinguserlogin');
Route::get('/simulationdelete/{id}', 'SimulationController@destroy')->name('simulationdelete');
Route::get('/start_chat/{id}', 'HomeController@start_chat')->name('start_chat');
Route::get('/users/{id}', 'HomeController@users')->name('users');
Route::get('/leaveroom/{id}', 'SimulationController@leaveroom')->name('leaveroom');
Route::post('/userlogout', 'SimulationController@userlogout')->name('userlogout');
Route::post('/brodcastmessage', 'SimulationController@brodcastmessage')->name('brodcastmessage');
Route::post('/welcomestatus', 'SimulationController@welcomestatus')->name('/welcomestatus');
Route::post('/statuschange', 'SimulationController@statuschange')->name('/statuschange');
Route::post('/linksearch', 'SearchController@linksearch')->name('/linksearch');
Route::post('/featuredlink/search', 'SearchController@featuredlink')->name('/featuredlink');
Route::get('/get-chat/{user}', 'MessageController@getChats')->name('getChats');
