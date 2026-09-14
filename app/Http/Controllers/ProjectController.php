<?php

namespace App\Http\Controllers;

class ProjectController extends Controller
{
    // The variable names should ideally match the route parameters
    public function show(string $categoryId, string $projectId)
    {
        return 'Category: '.$categoryId.' | Project: '.$projectId;
    }
}
