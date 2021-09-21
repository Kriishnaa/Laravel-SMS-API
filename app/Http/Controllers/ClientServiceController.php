<?php

namespace App\Http\Controllers;

use App\Models\ClientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ClientServiceResource;
use App\Models\Client;
use App\Models\Service;
use App\Traits\ResponseAPI;
 
class ClientServiceController extends Controller
{
    use ResponseAPI;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $client_services = ClientService::paginate();
        return $this->sendResponse(ClientServiceResource::collection($client_services), 'Client service retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'client_id' => 'required',
            'service_id'=> 'required',
            'twilio_sid'=>'required',
            'twilio_token'=>'required',
            'twilio_from'=>'required',
            'status'=>'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(!Client::where('status',1)->find($input['client_id']) || !Service::where('status',1)->find($input['service_id'])){
            return $this->sendError('Client or service not available.');
        }
        $config_data = json_encode(
            [
                'twilio_sid'=>$input['twilio_sid'],
                'twilio_token'=>$input['twilio_token'],
                'twilio_from'=>$input['twilio_from']
            ]
        );

        $client_service = new ClientService();
        $client_service->client_id = $input['client_id'];
        $client_service->service_id = $input['service_id'];
        $client_service->configuration_json_data = $config_data;
        $client_service->status = $input['status'];
        $client_service->save();

        return $this->sendResponse(new ClientServiceResource($client_service), 'Client service created successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ClientService  $clientService
     * @return \Illuminate\Http\Response
     */
    public function show(ClientService $clientService)
    {
        if (is_null($clientService)) {
            return $this->sendError('client service not found.');
        }
   
        return $this->sendResponse(new ClientServiceResource($clientService), 'Client service retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ClientService  $clientService
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ClientService $clientService)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'client_id' => 'required',
            'service_id'=> 'required',
            'twilio_sid'=>'required',
            'twilio_token'=>'required',
            'twilio_from'=>'required',
            'status'=>'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

        if(!Client::where('status',1)->find($input['client_id']) || !Service::where('status',1)->find($input['service_id'])){
            return $this->sendError('Client or service not available.');
        }

        $config_data = json_encode(
            [
                'twilio_sid'=>$input['twilio_sid'],
                'twilio_token'=>$input['twilio_token'],
                'twilio_from'=>$input['twilio_from']
            ]
        );

        $clientService->client_id = $input['client_id'];
        $clientService->service_id = $input['service_id'];
        $clientService->configuration_json_data = $config_data;
        $clientService->status = $input['status'];
        $clientService->save();

        return $this->sendResponse(new ClientServiceResource($clientService), 'Client service updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ClientService  $clientService
     * @return \Illuminate\Http\Response
     */
    public function destroy(ClientService $clientService)
    {
        $clientService->delete();
        return $this->sendResponse([], 'client service deleted successfully.');
    }
 
}
