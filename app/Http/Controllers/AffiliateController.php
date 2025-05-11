<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Http\Requests\UploadAffiliates;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    protected $affiliateService;

    /**
     * Create a new controller instance.
     *
     * @param AffiliateService $affiliateService The affiliate service instance.
     */
    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Load affiliates from the source file or an uploaded file.
     *
     * @param UploadAffiliates $request The HTTP request object.
     * @return JsonResponse The JSON response containing the affiliates.
     */
    public function inviteAffiliates(UploadAffiliates $request): JsonResponse
    {
        try {
            $affiliates = $this->affiliateService->loadAffiliates(
                $request->file('affiliates')
            );
            return response()->json(['affiliates' => $affiliates], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
