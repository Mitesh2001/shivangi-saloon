<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;

use App\Helpers\Helper;
use App\Models\SMSTemplate; 
use App\Models\EmailTemplate; 

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendEmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Will send email on events.';

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
        $reminderTemplates = EmailTemplate::where('event_type', 'reminder')->get();
        $birthdayTemplates = EmailTemplate::where('event_type', 'birthday')->get();
        $anniversaryTemplates = EmailTemplate::where('event_type', 'anniversary')->get();
        $dateTemplates = EmailTemplate::where('event_type', 'date')->whereDate('event_date', '=', date('Y-m-d'))->get();
        
        Helper::sendEventEmail($dateTemplates, 'date');
        Helper::sendEventEmail($birthdayTemplates, 'birthday'); 
        Helper::sendEventEmail($anniversaryTemplates, 'anniversary'); 
        Helper::sendEventEmail($reminderTemplates, 'reminder');

        // \Log::info("email Send!"); 
    }
}
