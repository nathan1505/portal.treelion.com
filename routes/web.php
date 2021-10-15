<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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


//---- Basic functions ----

Route::get('/', function () {
    return view('home');
})->middleware('auth')->middleware('verified');

Route::get('/logout', function(){
    Auth::logout();
    return redirect('/');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

//Get Hang Seng Index from the database
Route::get('/get-hsi', 'App\Http\Controllers\HsiController@GetHsi');
//Get elion stock price from the database
Route::get('/get-elion', 'App\Http\Controllers\HsiController@GetElion');
//Get latest economic news from database
Route::get('/get-news', 'App\Http\Controllers\HsiController@GetNews');

//---- End of Basic functions ----


//---- Routers of main-page functions ----

//Post request to create a new announcement
Route::post('post-announcement', 'App\Http\Controllers\AnnouncementsController@postannouncement')->middleware('auth');
//Get all announcements from database
Route::get('/get-announcements', 'App\Http\Controllers\AnnouncementsController@GetAnnouncements');

//---- End of routers of main-page functions


//---- Routers of User function ----

//Get the info of users with role as "manager"
Route::get('/get-managers', 'App\Http\Controllers\UsersController@GetManagers');
//Get the info of all users
Route::get('/performance/get-users', 'App\Http\Controllers\UsersController@GetMembers');
//Get the company name of specific user_id
Route::get('/daily/get-company-name/{id}', 'App\Http\Controllers\DailyController@GetCompany');

//---- End of Routers of User function ----


//---- Routers for Performance work ----

//Render the page of creating a performance work
Route::get('/performance/register', function(){
    return view('performance.register');
})->middleware('auth');
//Render the detail page of one performance work, seached by duty ID
Route::get('/duties/{duty_id}', function(){
    return view('performance.duty');
})->middleware('auth')->whereNumber('duty_id');
//Render the page of all performance duties, together with modifying functions
Route::get('/duties', function(){
    return view('performance.all-duties');
})->middleware('auth');

//Get all performance duties
Route::get('/get-performances', 'App\Http\Controllers\PerformancesController@GetAllPerformances');
//Get performance duties by user ID
Route::get('/get-performance/{id}', 'App\Http\Controllers\PerformancesController@GetPerformanceDutiesByUserId');
//Get performance duty by duty ID
Route::get('/get-duty-detail/{duty_id?}', 'App\Http\Controllers\PerformancesController@SeeDuty');
//Get nodes information of specific performance work, search by the performance ID
Route::get('/get-nodes/{duty_id}', 'App\Http\Controllers\PerformancesController@SeeNodes');
//Get the json of performance duty, to generate the duties table
Route::get('/duties/generate-duties-table', 'App\Http\Controllers\PerformancesController@GetDutiesTable');
//Update the information of performance duty table
Route::get('/duties/update-duty-table', 'App\Http\Controllers\PerformancesController@UpdatePerformanceDuty');
//Update the total points gained by all users
Route::get('/duties/update-total-points', 'App\Http\Controllers\PerformancesController@UpdateTotalPoints');
//Update the month points gained by all users
Route::get('/duties/update-month-points', 'App\Http\Controllers\PerformancesController@UpdateMonthPoints');

//Post request to create a new Performance duty
Route::post('/performance/post-duty', 'App\Http\Controllers\PerformancesController@PostDuty')->middleware('auth');
//Post method for update the node of one Performance duty
Route::post('/duties/post-node', 'App\Http\Controllers\PerformancesController@UpdateNode');
//Post method for approve or reject a performance duty
Route::post('/duties/check-duty', 'App\Http\Controllers\PerformancesController@CheckDuty');

//---- End of Routers for performance duty ---

//---- Routers for basic work ---
//Render the page of creating a basic work
Route::get('/basic/register', function(){
    return view('basic.register');
})->middleware('auth');

//Post request to create a new basic work
Route::post('/basic/post-duty', 'App\Http\Controllers\BasicController@PostDuty')->middleware('auth');

//---- End of Routers for basic work----

//---- Routers for daily report ----

//Render the daily page of the current user
Route::get('/daily/{id?}', function ($id) {
    return view('daily.daily')->with('userId', $id);
})->middleware('auth');
//Render page of morning daily report
Route::get('/daily-register', function(){
    return view('daily.register');
})->middleware('auth');
//Render page of evening daily report
Route::get('/daily/update/{id?}/{date?}', function(){
    return view('daily.update');
})->middleware('auth');
//Render detail page of specific daily report
Route::get('/daily/detail/{id?}', function($id){
    return view('daily.detail')->with('dailyReportId', $id);
})->middleware('auth');

//Get the daily report data of specific user
Route::get('/daily/get-daily/{id?}', 'App\Http\Controllers\DailyController@GetDaily');
//Get the daily report by daily report id
Route::get('/daily/get-daily-by-id/{id}', 'App\Http\Controllers\DailyController@GetDailyById');
//Get the daily report of user on some date
Route::get('/daily/get-daily/{id?}/{date?}', 'App\Http\Controllers\DailyController@GetDailyUpdate');
//Get the daily report data of specific user, for generating the calendar
Route::get('/daily/generate-daily-calendar/{id}', 'App\Http\Controllers\DailyController@GenerateDailyCalendar');

//Generate pdf and img for daily report
Route::get('/daily/img/{id?}', 'App\Http\Controllers\DailyController@GenerateImg')->middleware('auth');
Route::get('/daily/pdf/{id?}', 'App\Http\Controllers\DailyController@GeneratePdf')->middleware('auth');

//Post method for morning daily report
Route::post('/daily/post-duty', 'App\Http\Controllers\DailyController@PostDaily');
//Post method for updating daily report
Route::post('/daily/update-daily', 'App\Http\Controllers\DailyController@UpdateDaily');

//---- End of Routers for performance duty ---

//---- Routers for PDF view page ----
//Get the contact list in pdf form
Route::get('/pdf_contact_list', 'App\Http\Controllers\PDFsController@pdfContactList');

