<?php


namespace Inensus\OdysseyS3Integration\Http\Controllers;


use Illuminate\Http\Request;
use Inensus\OdysseyS3Integration\Http\Resources\S3Resource;
use Inensus\OdysseyS3Integration\Services\S3HistoryService;

class S3HistoryController extends Controller
{
    private $historyService;

    public function __construct(S3HistoryService $historyService)
    {
        $this->historyService = $historyService;
    }

    public function index(Request $request):S3Resource
    {
        return new S3Resource($this->historyService->getHistoryByTag($request));
    }
}
