<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\UserProfileActions;
use App\Http\Requests\UserFileUploadRequest;
use App\Models\ShuftiproJurisdictionCode;

class ApiProfileController extends Controller
{
    //Store and modify profile details of users
    public function savePersonalDetails(Request $request)
    {
        $personalDetail = (new UserProfileActions())->createPersonalDetails($request);
        return $personalDetail;
    }

    //store user addres
    public function savePersonalAddress(Request $request)
    {
        $personalDetail = (new UserProfileActions())->createAddress($request);
        return $personalDetail;
    }

    //store kyc response
    public function saveKycResponse(Request $request)
    {
        $saveResponse = (new UserProfileActions())->kycResponse($request);
        return $saveResponse;
    }

    //store kyc details
    public function saveKyc(Request $request)
    {
        $saveKyc = (new UserProfileActions())->createKycDetails($request);
        return $saveKyc;
    }

    public function userUploadDocs(UserFileUploadRequest $request)
    {
        $uploadDoc = (new UserProfileActions())->userUpload($request);
        return $uploadDoc;
    }
    public function fetchAllUploads($per_page)
    {
        $allUploads = (new UserProfileActions())->getAllUploads($per_page);
        return $allUploads;
    }

    public function filterUploadDocs(Request $request)
    {
        $filterDoc = (new UserProfileActions())->fetchUpload($request);
        return $filterDoc;
    }

    /**
     * Business Account
     */

    public function registerBusiness(Request $request)
    {
        $saveBusiness = (new UserProfileActions())->business_signup($request);
        return $saveBusiness;
    }

    //Get Shuft Jurisdiction Code
    public function getShuftiProJurisdictionCode()
    {
        $jurisdictionCode = ShuftiproJurisdictionCode::all(); // Fetch all jurisdiction code
        $message = "Jurisdiction Code Fetched";
        return $this->jsonResponse(HTTP_CREATED, $message, $jurisdictionCode);
    }

    //Update business
    public function updateBusiness(Request $request, $uuid)
    {
        $updateBusinesses = (new UserProfileActions())->updateBusinessDetails($request, $uuid);
        return $updateBusinesses;
    }

    //Fetch user busines on their login page
    public function getMyBusinessDetails(Request $request)
    {
        $fetch_business = (new UserProfileActions())->fetchUserBusiness($request);
        return $fetch_business;
    }

    //Fetch user busines on their login page
    public function deleteBusinessAccount()
    {
        $deleteBusiness = (new UserProfileActions())->deleteBusiness();
        return $deleteBusiness;
    }
}
