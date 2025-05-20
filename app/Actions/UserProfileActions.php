<?php

namespace App\Actions;

use App\Http\Requests\UserFileUploadRequest;
use App\Http\Resources\BusinessAccountResources;
use App\Http\Resources\UserResources;
use App\Http\Resources\UserUploadResources;
use App\Models\BusinessUserAccount;
use App\Models\Idtype;
use App\Traits\HasJsonResponse;
use App\Traits\ModelTraits;
use Illuminate\Notifications\Notifiable;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\User;
use App\Models\UserUpload;
use App\Notifications\BusinessAccountCreation;
use App\Notifications\KycVerificationNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserProfileActions
{
    use HasJsonResponse, ModelTraits, Notifiable;

    protected $excludeFromNonCompliance = [
        'cosmosec_occupation_industry',
        'personal_details_at',
        'call_shuftipro_at',
        'kyc_details_at',
        'address_details_at',
        'kyc_reference_no',

        // Address details
        'address_line_1',
        'address_line_2',
        'unit_no',
        'street_no',
        'street_name',
        'suburb',
        'postcode',

        // Personal details
        'firstname',
        'lastname',
        'othernames',
        'dob',
        'gender',
        'occupation',
        'shufti_profile_at',


        'state_id',
        'country_id',
        'statem',
        'estimated_monthly_send',
        'face_verified_at',
        'face_verification_metas'
    ];
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createPersonalDetails(Request $request)
    {

        try {
            $user = current_user();
            $profile = $user->profile;
            $profile->firstname = $request->firstname ?? $profile->firstname;
            $profile->lastname = $request->lastname ?? $profile->lastname;
            $profile->othernames = $request->othernames ?? $profile->othernames;
            $profile->dob = $request->dob ?? $profile->dob;
            $profile->gender = $request->gender ?? $profile->gender;
            $profile->occupation = $request->occupation ?? $profile->occupation;
            $profile->occupation_industry = $request->occupation_industry ?? $profile->occupation_industry;
            $profile->shufti_profile_at = NULL;
            $profile->cosmosec_occupation_industry = $request->occupation_industry_id ?? $profile->cosmosec_occupation_industry;
            $profile->kyc_at = NULL;
            $profile->personal_details_at = now();

            if ($profile->kyc_details_at && $profile->address_details_at) {
                $profile->call_shuftipro_at = now()->addMinutes(config('shufti_pro.shufti_retry_minute'));
            }

            $isSentToCompliance = $this->changeToNonCompliance($profile);

            $profile->save();

            $message = ($isSentToCompliance)
                ? 'Thank you for making corrections on your profile. Please contact compliance@cosmoremit.com to activate your profile'
                : 'Thank you for the update.';

            return $this->jsonResponse(HTTP_CREATED, $message, [new UserResources($user)]);
        } catch (\Exception $ex) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, 'Profile update failed');
            Log::error('Profile update failed: ' . $ex->getMessage());
        }
    }

    public function createAddress(Request $request)
    {
        try {
            $user = current_user();
            $profile = $user->profile;

            $profile->country_id = $request->country_id;

            $state = State::find($request->state);

            $profile->address_line_1 = $request->address ?? $profile->address_line_1;
            $profile->unit_no = $request->unit_no ?? $profile->unit_no;
            $profile->street_no = $request->street_no ?? $profile->street_no;
            $profile->state_id = $state->id ?? $profile->state_id;
            $profile->street_name = $request->street_name ?? $profile->street_name;
            $profile->suburb = $request->suburb ?? $profile->suburb;
            $profile->postcode = $request->postal_code ?? $profile->postcode;
            $profile->shufti_profile_at = null;
            $profile->kyc_at = null;
            $profile->address_details_at = now();

            if ($profile->kyc_details_at && $profile->personal_details_at) {
                $profile->call_shuftipro_at = now()->addMinutes(config('shufti_pro.shufti_retry_minute'));
            }

            $isSentToCompliance = $this->changeToNonCompliance($profile);

            $profile->save();

            $message = ($isSentToCompliance)
                ? 'Thank you for making corrections on your profile. Please contact compliance@cosmoremit.com to activate your profile'
                : 'Thank you for the update.';

            return $this->jsonResponse(HTTP_CREATED, $message, [new UserResources($user)]);
        } catch (\Exception $ex) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, 'Address update failed');
        }
    }

    private function changeToNonCompliance($profile): bool
    {
        $yesChange = false;

        $dirtyAttributes = $profile->getDirty();

        log_activity($profile, 'User updated profile', $dirtyAttributes);

        foreach ($dirtyAttributes as $field => $value) {
            if (!in_array($field, $this->excludeFromNonCompliance)) {
                $yesChange = true;
                break;
            }
        }

        if ($yesChange) {

            $profile->compliance_reason = 'Edited profile';
        }

        return $yesChange;
    }

    public function kycResponse(Request $request)
    {
        $user = current_user();
        $shufti_response = json_encode($request);
        $user->profile->shufti_response = $shufti_response;

        if ($request->event == 'verification.accepted') {
            $user->profile->shufti_profile_at = now();
        }
        $user->profile->save();


        if (!$request->event == 'verification.declined') {
            $user->notify(new KycVerificationNotification($request->event, $request->declined_reason));
        }

        $user->notify(new KycVerificationNotification($request->event));

        return $this->jsonResponse(HTTP_CREATED, "Thank you for making corrections on your profile. Please wait for our compliance team\'s approval.");
    }

    public function createKycDetails(Request $request)
    {
        try {
            $user = current_user();
            $profile = $user->profile;

            $idtype = Idtype::where('id', $request->id_type)->where('country_id', $profile->country_id)->first();

            if ($idtype) {
                $idtype_metas = json_decode($request->idtype_metas, true);
                if ($idtype->metas && is_null($request->idtype_metas)) {
                    return $this->jsonResponse(HTTP_BAD_REQUEST, "Required fields are missing: " . json_encode($idtype->metas));
                }
                if ($idtype->metas) {
                    if (
                        array_key_exists('required', $idtype_metas) && !is_null($idtype_metas['required'])
                    ) {
                        foreach ($idtype_metas['required'] as $id_meta => $meta) {
                            // check for null field in the required fields
                            if (!$meta) {
                                return $this->jsonResponse(HTTP_BAD_REQUEST, "Required field " . $id_meta . " is empty");
                            }
                        }
                    }
                }
            } else {
                return $this->jsonResponse(HTTP_BAD_REQUEST, "Selected Idtype does not apply to user country.");
            }

            $profile->idtype_metas = $idtype_metas['required'] ?? $profile->idtype_metas;
            $profile->idtype_id = $request->id_type ?? $profile->idtype_id;
            $profile->card_issuer = $idtype->issuer ?? $profile->card_issuer;
            $profile->idnumber = $request->id_number ?? $profile->idnumber;
            $profile->card_number = $request->card_number ?? $profile->card_number;
            $profile->id_issue_date = $request->issue_date ?? $profile->id_issue_date;
            $profile->id_expiry_date = $request->expiry_date ?? $profile->id_expiry_date;
            $profile->kyc_reference_no = Str::random(10) ?? $profile->kyc_reference_no;


            if ($request->scanned_id_front) {
                $profile->scanned_id_front = 'scanned_id_front';
            }

            if ($request->scanned_id_rear) {
                $profile->scanned_id_rear = 'scanned_id_rear';
            }

            $profile->kyc_details_at = now();
            $profile->shufti_profile_at = null;
            $profile->kyc_at = null;

            if ($profile->address_details_at && $profile->personal_details_at) {
                $profile->call_shuftipro_at = now()->addMinutes(config('shufti_pro.shufti_retry_minute'));
            }

            $isSentToCompliance = $this->changeToNonCompliance($profile);

            $profile->save();
            $message = ($isSentToCompliance)
                ? 'Thank you for making corrections on your profile. Please contact compliance@cosmoremit.com to activate your profile'
                : 'Thank you for the update.';

            return $this->jsonResponse(HTTP_CREATED, $message, [new UserResources($user)]);
        } catch (\Exception $ex) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, 'KYC update failed');
        }
    }

    public function userUpload(UserFileUploadRequest $request)
    {
        $user = current_user();

        $files = documan()
            ->setDisk('user_docs')
            ->medium()
            ->upload($request, 'docs');

        //save upload details to user_uploads table
        if ($files) {
            $fileUploads = $user->profile->userUploads()->create([
                'title'    => ucfirst($request->title),
                'message'  => ucfirst($request->message),
            ]);

            //save the file(s) into business_file_upload table
            foreach ($files as $file) {
                $fileUploads->userFilesUploads()->create([
                    'file_path' => $file['base_name']
                ]);
            }
            $message = "File(s) Uploaded SUcessfully";
            return $this->jsonResponse(HTTP_CREATED, $message, new UserUploadResources($fileUploads));
        }
    
    }

    public function fetchUpload($request)
    {
        $filter = [];
        $user = current_user();
        $profile = $user->profile;

        if ($request->date) {
            $filter = UserUpload::where('profile_id', $profile->id)->where('title', 'LIKE', '%' . $request->document_title . '%')
                ->orWhereDate('created_at', Carbon::parse($request->date)->toDateString())
                ->with('userFilesUploads')->orderBy("created_at", 'desc')->paginate(10);
        } else {
            $filter = UserUpload::where('title', 'LIKE', '%' . $request->title . '%')->where('profile_id', $profile->id)
                ->with('userFilesUploads')->orderBy("created_at", 'desc')->paginate(10);
        }

        return $filter;
    }

    public function getAllUploads($per_page)
    {
        $user = current_user();
        $profile = $user->profile;
        
        $allUploads = UserUpload::where('profile_id', $profile->id)->with('userFilesUploads')->orderBy('created_at','desc')->paginate($per_page ?? 10);
        return $allUploads;
    }

    /**
     * Business Profile Section
     */
    //Registration of new business
    public function business_signup(Request $request)
    {
        $user = current_user();
        $businessExists = BusinessUserAccount::where('user_id', $user->id)->exists();
        $reference_id = mt_rand(10000000, 99999999);

        // Check if User has a record of existing business
        if ($businessExists) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "You already have a Business account created earlier.");
        }

        try {
            //if no business created earlier, create the business
            $profile = $user->profile;
            $request->validate([
                'business_name'       =>   'required',
                'business_category'   =>   'required',
                'business_location'   =>   'required',
                'certificate_image'   =>   'required|mimes:jpeg,png,jpg,gif,pdf|max:5120',
                'incorporation_date'  =>   'required',
                'company_registration_number'  =>   'required',
                'company_jurisdiction_code'  =>   'required',
            ]);

            // Create a new BusinessAccount instance
            $businessAccount = new BusinessUserAccount([
                'business_reference_id' => $reference_id,
                'business_name'         => $request->business_name,
                'business_category'     => $request->business_category,
                'business_verify_status' => BusinessUserAccount::PENDING,
                'business_location'     => $request->business_location,
                'certificate_image'     => 'certificate_image',
                'incorporation_date'    => $request->incorporation_date,
                'registration_number'  => $request->company_registration_number,
                'jurisdiction_code'  =>   $request->company_jurisdiction_code,

            ]);

            // Save the business account to the database
            $user->businessUserAccounts()->save($businessAccount);
            $user->notify(new BusinessAccountCreation($profile->firstname, $request->business_name));

            $message = "Business account created successfully";
            return $this->jsonResponse(HTTP_CREATED, $message, new BusinessAccountResources($businessAccount));
        } catch (\Exception $e) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Failed to create business account, please try again later.");
        }
    }

    //find busines by uuid
    public function getBusinessByUuid($uuid)
    {
        //find the business using uuid
        $userBusiness = BusinessUserAccount::where('uuid', $uuid)->first();
        return $userBusiness;
    }

    //Update of new Business
    public function updateBusinessDetails(Request $request, $uuid)
    {
        // Find the existing BusinessAccount instance

        $businessAccount = $this->getBusinessByUuid($uuid);

        try {
            // Update the BusinessAccount instance
            $businessAccount->business_name = $request->business_name ?? $businessAccount->business_name;
            $businessAccount->business_category = $request->business_category ?? $businessAccount->business_category;
            $businessAccount->business_location = $request->business_location ?? $businessAccount->business_location;
            $businessAccount->incorporation_date = $request->incorporation_date ?? $businessAccount->incorporation_date;
            $businessAccount->business_verify_status = BusinessUserAccount::PENDING;
            $businessAccount->registration_number = $request->company_registration_number ?? $businessAccount->registration_number;
            $businessAccount->jurisdiction_code = $request->company_jurisdiction_code ?? $businessAccount->jurisdiction_code;

            if (!empty($request->certificate_image)) {
                $businessAccount->certificate_image = 'certificate_image';
            }

            // Save the changes to the database
            $businessAccount->save();
            $message = "Business account updated successfully";
            return $this->jsonResponse(HTTP_CREATED, $message, new BusinessAccountResources($businessAccount));
        } catch (\Exception $e) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Failed to update business account, please try again later.");
        }
    }

    //method to get a single business
    public function getUserBusiness($id)
    {
        //find the business created by the logged in user
        $business = BusinessUserAccount::where('user_id', $id)->first();
        return $business;
    }

    public function fetchUserBusiness()
    {
        try {
            //Display details of a business for user on their account when they login
            $user = current_user();
            $businessAccount = $this->getUserBusiness($user->id);
            $business_details = BusinessUserAccount::where('user_id', $businessAccount->user_id)
                ->whereNull('deleted_at')
                ->first();

            if (!$business_details) {
                return $this->jsonResponse(HTTP_BAD_REQUEST, "Could not retrieve data at the moment.");
            }
            $message = "Business details retrieved";
            return $this->jsonResponse(HTTP_CREATED, $message, new BusinessAccountResources($businessAccount));
        } catch (\Exception $ex) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Failed to complete your request.");
        }
    }

    //Delete a business account
    public function deleteBusiness()
    {
        try {
            $user = current_user();
            $businessUserAccount = BusinessUserAccount::where('user_id', $user->id)->first();

            if ($businessUserAccount) {

                $businessUserAccount->business_verify_status = BusinessUserAccount::PENDING;

                // Update the verification status of business
                $businessUserAccount->update(['business_verify_status' => $businessUserAccount->business_verify_status]);

                // Delete the record
                $businessUserAccount->delete();

                $message = "Business account deleted successfully";
                return $this->jsonResponse(HTTP_CREATED, $message);
            } else {
                return $this->jsonResponse(HTTP_BAD_REQUEST, "Failed to delete business.");
            }
        } catch (\Exception $ex) {
            return $this->jsonResponse(HTTP_BAD_REQUEST, "Failed to complete your request.");
        }
    }
}
