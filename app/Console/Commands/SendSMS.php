<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use App\Helpers\Helper;
use App\Models\SMSTemplate; 
use App\Models\EmailTemplate; 

class SendSMS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendSMS';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will send sms on events.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $birthdayTemplates = SMSTemplate::where('event_type', 'birthday')->get();
        $anniversaryTemplates = SMSTemplate::where('event_type', 'anniversary')->get();
        $dateTemplates = SMSTemplate::where('event_type', 'date')->whereDate('event_date', '=', date('Y-m-d'))->get();
        
        Helper::sendEventSMS($dateTemplates, 'date');
        Helper::sendEventSMS($birthdayTemplates, 'birthday'); 
        Helper::sendEventSMS($anniversaryTemplates, 'anniversary');   
  
        // \Log::info("SMS Send!"); 
    }
}
