<?php

namespace App\Http\Controllers;

use App\Models\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClientServiceResource;
use App\Models\Client;
use App\Models\Service;
use App\Models\SmsOperation;
use App\Traits\ResponseAPI;
use Exception;
use Twilio\Rest\Client as TwilioPackage;
use Twilio\Http\CurlClient;

class SmsOperationController extends Controller
{
    use ResponseAPI;

    /**
        * Sending SMS based on details
        * @return \Illuminate\Http\Response
    */
    public function sms_sending(Request $request)
    {
        $input = $request->all();
        //Basic Validation
        $validator = Validator::make($input, [
            'client_id' => 'required',
            'service_id'=> 'required',
            'receiver_number'=>'required',
            'sms_content'=>'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        //Valid Client & Service
        if(!Client::where('status',1)->find($input['client_id']) || !Service::where('status',1)->find($input['service_id'])){
            return $this->sendError('Client or service not available.');
        }

        //Metadata about client&Service
        $clientServiceData = ClientService::where(['client_id'=>$input['client_id'],'service_id'=>$input['service_id']])->get();

        if($clientServiceData[0]->status === 0){
            //Service Deactivated message.
            return $this->sendError('This service is not applicable.');
        }else{
            //Twilio API Processing
            $receiverNumber = $input['receiver_number'];
            $message = $input['sms_content'];
            $twilio_data = json_decode($clientServiceData[0]->configuration_json_data);
            try {
                $account_sid = $twilio_data->twilio_sid;
                $auth_token =  $twilio_data->twilio_token;
                $twilio_number = $twilio_data->twilio_from;
                
                $client = new TwilioPackage($account_sid, $auth_token);
                $curlOptions = [ CURLOPT_SSL_VERIFYHOST => false, CURLOPT_SSL_VERIFYPEER => false];
                $client->setHttpClient(new CurlClient($curlOptions));
                
                $client->messages->create($receiverNumber, [
                    'from' => $twilio_number, 
                    'body' => $message]);
                
                $sms_meta_data = new SmsOperation();
                $sms_meta_data->client_service_id = $clientServiceData[0]->id;
                $sms_meta_data->receiver_number = $receiverNumber;
                $sms_meta_data->sms_content = $message;
                $sms_meta_data->save();
                return $this->sendResponse([],'SMS Sent Successfully.');
            } catch (Exception $e) {
                return $this->sendError($e->getMessage());
            }
        }
    }
}
