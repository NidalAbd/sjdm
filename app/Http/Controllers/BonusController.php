<?php

namespace App\Http\Controllers;



class BonusController extends Controller
{
    public function requestBonus()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Check if the user qualifies for the bonus
        if ($user->canRequestBonus()) {
            // Process the bonus request here
            // ...

            return redirect()->back()->with('success', 'Your bonus request has been submitted!');
        } else {
            // Redirect back with an error message
            return redirect()->back()->with('error', 'You do not qualify for the bonus yet. Please refer 50 verified and active users.');
        }
    }

}
