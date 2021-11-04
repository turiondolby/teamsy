<?php

namespace App\Http\Livewire\Auth;

use App\User;
use App\Tenant;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Providers\RouteServiceProvider;

class Register extends Component
{
    /** @var string */
    public $name = '';

    /** @var string */
    public $companyName = '';

    /** @var string */
    public $email = '';

    /** @var string */
    public $password = '';

    public function register()
    {
        $this->validate([
            'name' => ['required', 'string'],
            'companyName' => ['required', 'string', 'unique:tenants,name'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
        ]);

        $tenant = Tenant::create([
            'name' => $this->companyName,
        ]);

        $user = User::create([
            'email' => $this->email,
            'name' => $this->name,
            'role' => 'admin',
            'password' => Hash::make($this->password),
            'tenant_id' => $tenant->id,
        ]);

        $user->sendEmailVerificationNotification();

        Auth::login($user, true);

        redirect(route('home'));
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
