<?php
use App\Http\Controllers\TaskController; // Ditambahkan
use Illuminate\Support\Facades\Route;

/*git add .
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/tasks/', [TaskController::class, 'index'])->name('tasks.index');

// Ditambahkan
Route::get('/tasks/{id}/edit', [TaskController::class, 'edit'])->name('tasks.edit');

Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
