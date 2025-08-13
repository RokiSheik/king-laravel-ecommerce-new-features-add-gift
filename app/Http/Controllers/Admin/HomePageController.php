<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\HomePageService;
use App\Http\Requests\HomePageRequest;

class HomePageController extends Controller
{
    protected $service;

    public function __construct(HomePageService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->get();
        return response()->json(['data' => $data]);
    }

    public function update(HomePageRequest $request)
    {
        return $this->service->update($request);
    }
}
