<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    public function changeLanguage($lang)
    {
        // Ensure the user is authenticated
        if (Auth::check()) {
            // Update the user's language in the database
            $user = Auth::user();
            $user->language = $lang;
            $user->save();
        }

        // Change the application locale immediately for the current session
        app()->setLocale($lang);

        // Redirect back to the previous page or home
        return redirect()->back();
    }
}
