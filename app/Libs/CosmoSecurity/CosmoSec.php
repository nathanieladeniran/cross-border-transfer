<?php

namespace CosmoSecurity;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CosmoSec
{
    protected $auth_token;
    public $active_contact;
    protected $data;


    public function getBaseUrl(): mixed
    {
        return config('app.cosmosec_url');
    }

    public function getHeader(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->getAccessToken()
        ];
    }

    public function postHttp($url, $data = null, $withHeader = true)
    {
        $this->data = $withHeader ? Http::retry(3,10000, throw: false)->withHeaders($this->getHeader())->post($url, $data) : Http::retry(3,10000, throw: false)->post($url, $data);
        return $this;
    }

    public function getHttp($url, $data = null, $withHeader = true)
    {
        $this->data = $withHeader ? Http::retry(3,10000, throw: false)->withHeaders($this->getHeader())->get($url, $data) : Http::retry(3,10000, throw: false)->get($url, $data);
        return $this;
    }

    public function get()
    {
        $response = $this->data->json();
        if (! ($this->data->status() == 200)) {
            return [
                'status' => 'error',
                'status_code' => $this->data->status(),
                'message' => isset($response['message']) ? $response['message'] : null,
                'data' => isset($response['data']) ? $response['data'] : $response
            ];
        }

        return [
            'status' => "success",
            'status_code' => $this->data->status(),
            'message' => isset($response['message']) ? $response['message'] : null,
            'data' => $response['data']
        ];
    }

    /**
     * @return null|string
     */
    public function getAccessToken(): ?string
    {
        return config('app.cosmosec_token');
    }

    public function calculate_transaction_risk($request)
    {
        $data = [
            'weekly_transaction_volume' => $request['weekly_transaction_volume'],
            'number_of_beneficiaries' => $request['number_of_beneficiaries'],
            'destination_country' => $request['destination_country'],
            'customer_occupation_industry' => $request['customer_occupation_industry'],
            'purpose_of_transaction' => $request['purpose_of_transaction']
        ];

        $url = $this->getBaseUrl() . '/risk/transaction_score';
        $response = $this->postHttp($url, $data)->get();
        return $response;
    }

    public function calculate_profile_risk($user)
    {
        $data = [
            
        ];

        $url = $this->getBaseUrl() . '/risk/profile_score';
        $response = $this->postHttp($url, $data)->get();
        return $response;
    }

    public function get_purpose_of_transaction($id=null)
    {
        $url = $this->getBaseUrl() . '/weights/purposes?key='.$id;
        $response = $this->getHttp($url)->get();
        return $response;
    }

    public function get_occupation_industry()
    {
        $url = $this->getBaseUrl() . '/weights/occupation';
        $response = $this->getHttp($url)->get();
        return $response;

    }

    public function faceVerification($profile)
    {
        $url = $this->getBaseUrl() . '/identity/face_bio';

        if (! $profile->scanned_id_front) {
            return response_beam()->throwOops('First upload your ID in the KYC section to continue.', 422);
        }

        $data = [
            'identity_photo' => convertImageToBase64($profile->profile_photo), // Base64 encoded image data
            'identity_card' => convertImageToBase64($profile->scanned_id_front), // Base64 encoded image data
            'idtype' => $profile->idtype->type
        ];

        $response = $this->postHttp($url, $data)->get();
        return $response;
    }

    public function faceAndIdVerification($request)
    {
        $url = $this->getBaseUrl() . '/identity/document_verification';

        $profile = api_user()->profile;
        
        $data = [
            'identity_photo' => convertImageToBase64($profile->profile_photo), // Base64 encoded image data
            'identity_card' => convertImageToBase64($profile->scanned_id_front),
            'reference' => generate_token(12),
            'email' => $profile->user->email,
            'callback_url' => $request->callback_url,
            'country_iso2' => $profile->country->iso2,
            'verification_type' => 'face',
            'idtype' => $profile->idtype->type,
            'firstname' => $profile->firstname,
            'lastname' => $profile->lastname,
            'othernames' => $profile->othernames,
            'dob' => Carbon::parse($profile->dob)->format('Y-m-d'),
            'age' => now()->diffInYears($profile->dob),
            'issue_date' => Carbon::parse($profile->id_issue_date)->format('Y-m-d'),
            'expiry_date' => Carbon::parse($profile->id_expiry_date)->format('Y-m-d'),
            'document_number' => $profile->idnumber,
            'gender' => $profile->gender == 'male' ? 'M' : 'F'
        ];

        $response = $this->postHttp($url, $data)->get();
        return $response;

    }
}

