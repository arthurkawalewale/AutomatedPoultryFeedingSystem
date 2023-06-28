<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserEditController extends Controller
{
    public function index()
    {
        return view('auth.profile');
    }

    public function update(User $user, Request $request)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'contact' => $request->contact,
            'number_of_birds' => $request->number_of_birds,
            'updated_at' => now()
        ]);

        //return $this->success('profile','Profile updated successfully!');
        return redirect('/');
    }
}
