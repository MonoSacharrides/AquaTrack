<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SelectRolesController extends Controller
{
    public function index() {
        return Inertia::render('SelectRoles/Index');
    }
}
