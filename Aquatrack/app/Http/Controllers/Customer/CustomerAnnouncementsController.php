<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Announcements;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerAnnouncementsController extends Controller
{
     public function index()
    {
        $announcements = Announcements::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'date' => $announcement->created_at->format('Y-m-d'),
                    'status' => ucfirst($announcement->status),
                    'start_date' => $announcement->start_date?->format('Y-m-d'),
                    'end_date' => $announcement->end_date?->format('Y-m-d'),
                ];
            });

        return Inertia::render('Customer/Announcements', [
            'announcements' => $announcements,
        ]);
    }
}
