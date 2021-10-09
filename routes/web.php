<?php

use App\Http\Controllers\FormController;
use App\Models\Form;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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
//Route::get('/{slug}', [FormController::class, 'show']);



/*Laravel demo*/
Route::get('/', function () {
    //very dirty way - todo: rewrite it to Laravel syntax
    $myquery1 = "SELECT input_element_id, fe.order, ie.header
            FROM forms as f
                join form_elements as fe on f.id = fe.form_id
                join input_elements as ie on fe.id = ie.form_element_id
                join text_inputs as ti on ie.id = ti.input_element_id
            WHERE f.slug = '3d6c9699-0787-30d8-828b-e37c767a80a3';";
    $myquery2 = "SELECT input_element_id, fe.order, ie.header
            FROM forms as f
                join form_elements as fe on f.id = fe.form_id
                join input_elements as ie on fe.id = ie.form_element_id
                join number_inputs as ti on ie.id = ti.input_element_id
            WHERE f.slug = '3d6c9699-0787-30d8-828b-e37c767a80a3';";

    $text_i = DB::select(DB::raw($myquery1));
    foreach ($text_i as $i) {
        $i->type = "text";
    }
    $number_i = DB::select(DB::raw($myquery2));
    foreach ($number_i as $i) {
        $i->type = "number";
    }

    $arr = array_merge($text_i, $number_i);
    //dd($arr);
    function sortByOrder($a, $b) {
        return $a->order - $b->order;
    }

    usort($arr, 'sortByOrder');

    return view('app', [
        'forms' => $arr
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
