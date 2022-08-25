<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Datatables;

use App\Models\User;  
use App\Models\CampaignTemplate;  

use App\Http\Requests\Campaign\StoreCampaignRequest;
// use App\Http\Requests\Campaign\storeSMSCampaignRequest;


class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('campaign.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function createEmailCampaign()
    {
        $data['audience_types'] = $this->audience_types();
        return view('campaign.email.create')->with($data);
    }
    public function createSMSCampaign()
    {
        $data['audience_types'] = $this->audience_types();
        return view('campaign.sms.create')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCampaignRequest $request)
    {
        $type = $request->type;

        dd($type);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function audience_types() 
    {
        return [
            'All Customers', 'All Customers',
            'New Customers', 'New Customers',
            'Repeat Customers', 'Repeat Customers', 
            'Regular Customers', 'Regular Customers', 
            'Risk Customers', 'Risk Customers', 
            'Lost Customers', 'Lost Customers',   
            'No Risk', 'No Risk',   
            'At Risk', 'At Risk', 
        ];
    }
}
