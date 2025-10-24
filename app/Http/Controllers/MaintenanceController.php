<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Show maintenance page
     */
    public function index()
    {
        return view('maintenance');
    }
    
    /**
     * Enable maintenance mode (admin only)
     */
    public function enable()
    {
        // You can add logic here to set a maintenance flag in database
        // or create a maintenance file that other routes check
        
        return redirect()->route('maintenance')->with('success', 'Maintenance mode enabled');
    }
    
    /**
     * Disable maintenance mode (admin only)
     */
    public function disable()
    {
        // Remove maintenance flag or file
        
        return redirect('/')->with('success', 'Maintenance mode disabled');
    }
}
