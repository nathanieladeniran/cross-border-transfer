<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;

class BusinessCategoryController extends Controller
{
    public function index()
    {
        $categories = BusinessCategory::all();
        $message = "Categories Fetched";
        return $this->jsonResponse(HTTP_CREATED, $message, $categories);
    }
}
