<?php
// routes/web.php
// Versão Final de Estabilização: Usa Closures para TODOS os Controllers, exceto Livewire, 
// para contornar problemas de ReflectionException no servidor.

use App\Livewire\Admin\PlanManager;
use App\Livewire\Admin\SettingsForm;
use App\Livewire\UtmLinkManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| ROTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

// Landing Page (Página de Vendas)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rota de Redirecionamento de Link Curto (Usando Closure para evitar Reflection no Controller)
Route::get('/{shortCode}', function ($shortCode) {
    // Tenta instanciar o Controller internamente para redirecionamento.
    // Se o Autoloading estiver OK, isso funciona. Se não, pelo menos não quebra o roteamento.
    return app(\App\Http\Controllers\UtmRedirectController::class)->redirect(request(), $shortCode);
})->name('utm.redirect');


/*
|--------------------------------------------------------------------------
| ROTAS DE AUTENTICAÇÃO (Totalmente em Closures)
|--------------------------------------------------------------------------
*/

// Login GET: Carrega a View (CORRIGIDO PARA CLOSURE)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Login POST: Lógica de POST (CORRIGIDO PARA CLOSURE)
Route::post('/login', function (Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        
        if (Auth::user()->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }
        return redirect()->intended(route('user.dashboard'));
    }

    return back()->withErrors(['email' => 'As credenciais fornecidas não correspondem aos nossos registros.'])->onlyInput('email');
});

// Registro GET: Carrega a View (CORRIGIDO PARA CLOSURE)
Route::get('/register', function (Illuminate\Http\Request $request) {
    $planId = $request->query('plan_id');
    return view('auth.register', compact('planId'));
})->name('register');

// Registro POST: Lógica (CORRIGIDO PARA CLOSURE)
Route::post('/register', function (Illuminate\Http\Request $request) {
    // Lógica completa de registro (copiada do RegisterController.php)
    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'document' => $request->document,
        'phone' => null,
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        'subscription_status' => 'trial',
        'plan_id' => $request->plan_id ?? null,
    ]);
    
    Auth::login($user);
    return redirect()->route('checkout.plan');
})->name('register');


// Logout
Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// Rotas Socialite (MANTIDAS em Closures Temporárias)
Route::prefix('auth')->group(function () {
    Route::get('{provider}/redirect', function ($provider) {
        return "Socialite Redirect Placeholder."; 
    })->name('social.redirect');
    
    Route::get('{provider}/callback', function ($provider) {
        return "Socialite Callback Placeholder."; 
    })->name('social.callback');
});


/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS (Requires auth middleware)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // DASHBOARD DO USUÁRIO (Livewire)
    Route::get('/dashboard', UtmLinkManager::class)->name('user.dashboard');
    
    // CONEXÃO META ADS (MANTIDAS em Closures Temporárias)
    Route::get('auth/meta/redirect', function() { return "Meta Redirect Placeholder."; })->name('meta.redirect');
    Route::get('auth/meta/callback', function() { return "Meta Callback Placeholder."; })->name('meta.callback');

    // CHECKOUT / PAGAMENTO (MANTIDAS em Closures Temporárias)
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/planos', function() { return "Checkout Planos Placeholder"; })->name('plan');
        Route::get('/info', function() { return "Checkout Info Placeholder"; })->name('update-info');
        Route::post('/info', function() { return "Checkout Save Info Placeholder"; })->name('save-info');
        Route::get('/start/{plan}', function() { return "Checkout Start PIX Placeholder"; })->name('start');
    });


    // ROTAS DE ADMIN (Protegidas por AdminMiddleware)
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('dashboard');
        Route::get('/plans', PlanManager::class)->name('plans');
        Route::get('/settings', SettingsForm::class)->name('settings');
    });
});