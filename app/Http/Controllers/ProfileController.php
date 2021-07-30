<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        return Inertia::render('Profile', []);
    }

    public function update()
    {

        Request::validate([
            'username' => 'required|unique:users,username,' . Request::get('id'),
            'email' => 'required|email|unique:users,email,' . Request::get('id'),
            'name' => 'required',
        ]);

        User::updateOrCreate(
            ['id' => Request::get('id')],
            ['email' =>  Request::get('email'), 'username' =>  Request::get('username'), 'name' => Request::get('name')]
        );
        return Redirect::back()->with('success', 'Profile updated');
    }
}
