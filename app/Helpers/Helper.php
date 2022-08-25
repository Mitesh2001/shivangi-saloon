<?php

namespace App\Helpers;

// use App\Mail\SendEmail;
// use App\Mail\SendEmailViaQueue;
// use Storage, Mail;
use Carbon\Carbon;
use Storage;

use App\Models\Client;
use App\Models\User;
use App\Models\Branch;
use App\Models\Contacts;
use App\Models\Products;
use App\Models\Holiday;

use Image;
use Mail;
use App\Models\EmailLog;

use App\Models\SMSLog;
use App\Models\SMSTemplate;
use App\Models\Distributor;
use App\Models\SmsSettings;
use Auth;

class Helper
{
    // Is System User
    public static function is_distributor_user()
    {
        $user_type = Auth::user()->user_type;
        return $user_type == 2 ? true : false;
    }

    public static function is_system_user()
    {
        $user = Auth::user();
        $distributor_id = $user->distributor_id ?? 0;
        $user_type = $user->user_type;
        // Distributor id should be 0 & type = 0
        if($distributor_id == 0 && $user_type == 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getDistributorId()
    {
        $distributor_id = Auth::user()->distributor_id ?? 0;
        return $distributor_id;
    }

    // Returns array of reverse calcuated gst
    public static function getCalucatedGST($amount = 0, $sgst = 0, $cgst = 0, $igst = 0)
    {
        $amount = floatval($amount);
        $sgst = floatval($sgst);
        $cgst = floatval($cgst);
        $igst = floatval($igst);

        $total_tax = $sgst + $cgst + $igst;

        if(strlen($total_tax) <= 1) {
            $total_tax = '0'.$total_tax;
        }

        $formula = '1.'.$total_tax;

        $total_gst_amount = number_format(($amount - ($amount / $formula)),2,'.','');

        if($total_gst_amount == 0) {
            $sgst_amount = 0;
            $cgst_amount = 0;
            $igst_amount = 0;
        } else {
            $sgst_amount = number_format(($sgst * $total_gst_amount / $total_tax),2,'.','');
            $cgst_amount = number_format(($cgst * $total_gst_amount / $total_tax),2,'.','');
            $igst_amount = number_format(($igst * $total_gst_amount / $total_tax),2,'.','');
        }

        return [
            'total_gst_amount' => $total_gst_amount,
            'sgst_amount' => $sgst_amount,
            'cgst_amount' => $cgst_amount,
            'igst_amount' => $igst_amount,
        ];
    }

    /**
     *  Can create user
     *  will check number of users & compore with salon subscription
     */
    public static function canCreateUser($salon_id)
    {
        $salon = Distributor::find($salon_id);
        $number_of_users = User::where('distributor_id', $salon_id)->count();

        if($number_of_users >= $salon->no_of_users) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *  Can create branch
     *  will check number of branchs & compore with salon subscription
     */
    public static function canCreateBranch($salon_id)
    {
        $salon = Distributor::find($salon_id);
        $number_of_branches = Branch::where('distributor_id', $salon_id)->count();

        if($number_of_branches >= $salon->no_of_branches) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *  Check if key/value exists or not in multi dimantional array
     */
    public static function in_array_r($needle = null, $array = []) {
        return preg_match('/"'.preg_quote($needle, '/').'"/i' , json_encode($array));
    }

    /**
     *  Return boolean
     *  check if salon is on extra 14 days validity
     */
    public static function allowViewOnly($salon_id)
    {
        $salon_id = intval($salon_id);
        $salon = Distributor::find($salon_id);

        if($salon_id != 0 && !empty($salon)) {
            $expiry_date = $salon->expiry_date;

            if(!empty($expiry_date)){
                if($expiry_date < date('Y-m-d')){
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return true;
        }
    }


    public static function replace_view_status($status, $type = 'ACTIVE', $default = '-')
    {
        $status_array = [];
        switch ($type) {
            case 'ACTIVE':
                $status_array = config('global.status_array');
                break;
        }

        return (isset($status_array[$status]) ? $status_array[$status] : $default);
    }

    public static function get_new_filename($file)
    {
        $actual_name = \Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '-');
        $original_name = $actual_name;
        $extension = $file->getClientOriginalExtension();
        $i = 1;
        while ($exists = Storage::has($actual_name . "." . $extension)) {
            $actual_name = (string) $original_name . $i;
            $i++;
        }
        return $actual_name . "." . $extension;
    }

	public static function encrypt($string)
    {
        return $string;
        return $this->encrypt_decrypt("E", $string);
    }

    public static function decrypt($string)
    {
        return $string;
        return $this->encrypt_decrypt("D", $string);
    }

	private function encrypt_decrypt($action, $string)
    {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = env('APP_KEY');
        $secret_iv = 'RKCRM';

        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'E') {
            $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
        } else {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

	public static function getRecordOrder($sortOrder)
    {
        switch ($sortOrder) {
            case 'descend':
                $sortOrder = 'DESC';
                break;
            case 'ascend':
                $sortOrder = 'ASC';
                break;
            default:
                $sortOrder = 'DESC';
                break;
        }
        return $sortOrder;
    }

	public static function paginationData($request, $sortField = false)
    {
        if (!$request->size) {
            $request->size = 10;
        }
        if (!$request->sortField && !$sortField) {
            $request->sortField = 'created_at';
        }
        if ($sortField) {
            $request->sortField = $sortField;
        }
        if (!$request->sortOrder) {
            $request->sortOrder = "DESC";
        } else {
            $request->sortOrder = $this->getRecordOrder($request->sortOrder);
        }
        return $request;
    }

    public static function createImageFromBase64($file, $file_name, $path)
    {
        $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($file, 0, strpos($file, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);

        $fileName = $file_name ."_". time() .".". $extension;

        if(env('APP_ENV') == 'local') {
            $upoad_path =  $path . $fileName;
        } else {
            $upoad_path = 'public/'. $path . $fileName;
        }

        // Storage::put($fileName, base64_decode($newFile));
        Storage::disk('local')->put($upoad_path, base64_decode($newFile));

        $fullPath = 'storage/'. $path . $fileName;

        return $fullPath;
    }

    public static function resizeImageFromBase64($file, $file_name, $path)
    {
        $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($file, 0, strpos($file, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);
        $fileName = $file_name ."_". time() .".". $extension;
        $upoad_path =  $path . $fileName ;

        $image = Image::make(base64_decode($newFile))->resize(600, 600, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->save(public_path($upoad_path), 100);

        return $upoad_path;
    }

    // public static function createImageFromBase64($file)
    // {
    //     $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];   // .jpg .png .pdf
    //     $replace = substr($file, 0, strpos($file, ',') + 1);
    //     // find substring fro replace here eg: data:image/png;base64,
    //     $newFile = str_replace($replace, '', $file);
    //     $newFile = str_replace(' ', '+', $newFile);
    //     $fileName = \Str::random(10) . '.' . $extension;

    //     Storage::put($fileName, base64_decode($newFile));

    //     return $fileName;
    // }

    public static function createDocFromBase64($file, $old_filename)
    {
        $info = pathinfo($old_filename);
        $extension = $info['extension'];

        // $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];   // .jpg .png .pdf
        $replace = substr($file, 0, strpos($file, ',') + 1);
        // find substring fro replace here eg: data:image/png;base64,
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);
        $fileName = \Str::random(10) . '.' . $extension;

        Storage::put($fileName, base64_decode($newFile));

        return $fileName;
    }

    public static function createBase64FromImage($imageName)
    {
        if (Storage::has($imageName)) {
            $image_parts = explode(".", $imageName);
            $img_extension = $image_parts[1];
            $imageString = 'data:image/' . $img_extension . ';base64,' . base64_encode(Storage::get($imageName));
            return $imageString;
        }
        return $imageString = null;
    }

    public static function generateOTP($user, $min)
    {
        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expired_at = Carbon::now()->addMinutes($min);
        $user->save();

        return $otp;
    }

    public static function transformShortcodeValue($item, $obj)
    {
        $column = $item['column'];
        $shortcode = $item['shortcode'];

        if (strpos($column, '->') !== false) {
            $properties = explode("->", $column);
            $value = $shortcode;
            if (is_array($properties) && count($properties)) {
                $tmpObj = true;
                foreach ($properties as $key) {
                    if ($tmpObj && !is_object($tmpObj)) {
                        $tmpObj = (isset($obj->{$key}) ? $obj->{$key} : false);
                        if (!$tmpObj) break;
                    } else if (is_object($tmpObj)) {
                        $tmpObj = (isset($tmpObj->{$key}) ? $tmpObj->{$key} : false);
                        if (!$tmpObj) break;
                    }
                }
                $value = (!empty($tmpObj) ? $tmpObj : $shortcode);
            }
        } else {
            $value = ($obj && isset($obj->{$column}) ? $obj->{$column} : $shortcode);
        }

        switch ($column) {
            case 'login_url':
                $value = '#';
                break;
        }
        return $value;
    }

    public static function sendEventSMS($templates, $event_type)
    {
        foreach($templates as $template) {

            if($event_type == "date") {
                $clients = Client::where('distributor_id', $template->distributor_id)->get();

            } elseif ($event_type == "birthday") {
                $birthday_on = date('Y-m-d', strtotime("+$template->before_days day"));
                $clients = Client::whereRaw("MONTH(date_of_birth) = MONTH('$birthday_on') AND DAY(date_of_birth) = DAY('$birthday_on')")->where('distributor_id', $template->distributor_id)->get();

            } elseif ($event_type == "anniversary") {
                $anniversary_on = date('Y-m-d', strtotime("+$template->before_days day"));
                $clients = Client::whereRaw("MONTH(anniversary) = MONTH('$anniversary_on') AND DAY(anniversary) = DAY('$anniversary_on')")->where('distributor_id', $template->distributor_id)->get();
            }

            foreach($clients as $client) {
                $client_number = $client->primaryContact->primary_number;
                $message = $template->message;
                $message_body = Helper::getMessageBody($message, $client);

                $distributor = Distributor::find($template->distributor_id);
                $number_of_sms_used = ceil(strlen($message_body) / 160 );

                // check if salon has active email services
                if($distributor->sms_service == 0 || $distributor->total_sms < $number_of_sms_used) {
                    return false;
                }

                // Skip if no number found
                // Or skip if marketing notifications are off
                if(empty($client->primaryContact->primary_number) || $client->allow_notifications == 0) {
                    continue;
                }

                SMSLog::create([
                    'sender_id' => $distributor->sender_id,
                    'template_id' => $template->id,
                    'number_of_sms' => ceil(strlen($message_body) / 160),
                    'client_id' => $client->id,
                    'number' => $client_number,
                    'message_body' => $message_body,
                    'event_type' => $event_type,
                    'template_json' => json_encode($template),
                    'distributor_id' => $client->distributor_id,
                ]);

                $is_send = self::sendSingleSMS($client_number, $message_body);

                if($is_send) {
                    $distributor->used_sms += $number_of_sms_used;
                    $distributor->total_sms -= $number_of_sms_used;
                    $distributor->save();
                }
            }
        }
    }


    public static function sendAppointmentSMS($client, $sms_number, $appointment = false)
    {
        $distributor_id = $client->distributor_id;

        $distributor = Distributor::find($distributor_id);
        $template = SMSTemplate::where('name', 'Appointment SMS Template')->where('distributor_id', $distributor_id)->where('default_template', 0)->first();

        if(empty($template)) {
            $template = SMSTemplate::where('name', 'Appointment SMS Template')->where('default_template', 1)->first();
            // dd($templates);
        }
        $message = $template->message;
        $message_body = Helper::getMessageBody($message, $client, $appointment);

        $number_of_sms_used = ceil(strlen($message_body) / 160 );

        // check if salon has active sms services
        if($distributor->sms_service == 0 || $distributor->total_sms < $number_of_sms_used) {
            return false;
        }

        // Skip if no number found
        if(empty($client->primaryContact->primary_number)) {
            return false;
        }

        SMSLog::create([
            'sender_id' => $distributor->sender_id,
            'template_id' => $template->id,
            'number_of_sms' => ceil(strlen($message_body) / 160),
            'client_id' => $client->id,
            'number' => $sms_number,
            'message_body' => $message_body,
            'event_type' => "appointment",
            'template_json' => json_encode($template),
            'distributor_id' => $distributor->id
        ]);

        $is_send = self::sendSingleSMS($sms_number, $message_body);

        if($is_send) {
            $distributor->used_sms += $number_of_sms_used;
            $distributor->total_sms -= $number_of_sms_used;
            $distributor->save();
        }
        return true;
    }

    public static function sendEventEmail($templates, $event_type)
    {
        $clients = [];
        $salons = [];

        foreach($templates as $template) {

            if($event_type == "date") {
                $clients = Client::where('distributor_id', $template->distributor_id)->get();

            } elseif ($event_type == "birthday") {
                $birthday_on = date('Y-m-d', strtotime("+$template->before_days day"));
                $clients = Client::whereRaw("MONTH(date_of_birth) = MONTH('$birthday_on') AND DAY(date_of_birth) = DAY('$birthday_on')")->where('distributor_id', $template->distributor_id)->get();

            } elseif ($event_type == "anniversary") {
                $anniversary_on = date('Y-m-d', strtotime("+$template->before_days day"));
                $clients = Client::whereRaw("MONTH(anniversary) = MONTH('$anniversary_on') AND DAY(anniversary) = DAY('$anniversary_on')")->where('distributor_id', $template->distributor_id)->get();

            } elseif ($event_type == "reminder") {
                $expiry_on = date('Y-m-d', strtotime("+$template->before_days day"));
                $salons = Distributor::where('expiry_date', $expiry_on)->get();
            }

            foreach($salons as $salon) {
                $email = $salon->primary_email;

                // Skip if email is empty
                if(empty($email)) {
                    continue;
                }

                // Variable names remail same for salon's & clients so it wont create confusion
                $email_variable = array(
                    '{{#salon_name}}' => $salon->name,
                    '{{#client_name}}' => $salon->contact_person,
                    '{{#client_email}}' => $salon->primary_email,
                    '{{#client_contact_number}}' => $salon->primary_number,
                    '{{#client_whatsapp_number}}' => $salon->secondary_number,
                    '{{#contact_person}}' => $salon->contact_person,
                    '{{#subscription_expiry_date}}' => date('d-m-Y', strtotime($salon->expiry_date)),
                );

                $message_content = $template->content;
                foreach ($email_variable as $key => $value)
                    $message_content = str_replace($key, $value, $message_content);

                $emailTemplateBody =  Helper::emailTemplateBody();
                $emailTemplateBody =  str_replace("{{#template_body}}", $message_content, $emailTemplateBody);
                $message_content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody);

                $data['subject'] = $template->subject;
                $data['messagecontent'] = $message_content;
                $data['from_email'] =  "shivangi@noreplay.com";
                $data['from_name'] =  "ND Salon Software";

                EmailLog::create([
                    'template_id' => $template->email_template_id,
                    'client_id' => $salon->id,
                    'client_email' => $salon->primary_email,
                    'from_email' => "shivangi@noreplay.com",
                    'from_name' => "ND Salon Software",
                    'event_type' => $event_type,
                    'template_json' => json_encode($template),
                    'distributor_id' => 0,
                ]);

                Mail::send('emails.email', $data, function($message)use($data, $email) {
                    $message->subject($data['subject']);

                    if(isset($data['from_email']) && isset($data['from_name'])){
                        $message->from($data['from_email'], $data['from_name']);
                    }
                    $message->to($email);
                });
            }

            foreach($clients as $client) {

                $distributor = Distributor::find($template->distributor_id);

                $email = $client->primaryContact->email;

                // Skip if email is empty
                // Or skip if marketing notifications are off
                if(empty($email) || $client->allow_notifications == 0) {
                    continue;
                }

                $email_variable = array(
                    '{{#client_name}}' => $client->name,
                    '{{#client_email}}' => $email,
                    '{{#client_contact_number}}' => $client->primaryContact->primary_number,
                    '{{#client_whatsapp_number}}' => $client->primaryContact->secondary_number,
                    '{{#salon_name}}' => $distributor->name,
                    '{{#contact_person}}' => $distributor->contact_person,
                );

                $message_content = $template->content;
                foreach ($email_variable as $key => $value)
                    $message_content = str_replace($key, $value, $message_content);

                $emailTemplateBody =  Helper::emailTemplateBody();
                $emailTemplateBody =  str_replace("{{#template_body}}", $message_content, $emailTemplateBody);
                $message_content = str_replace("{{#all_css}}", Helper::emailTemplateCss(), $emailTemplateBody);



                // check if salon has active email services
                if($distributor->email_service == 0 || $distributor->total_email == 0) {
                    return false;
                }

                $data['subject'] = $template->subject;
                $data['messagecontent'] = $message_content;
                $data['from_email'] = $distributor->from_email;
                $data['from_name'] = $distributor->from_name;

                EmailLog::create([
                    'template_id' => $template->email_template_id,
                    'client_id' => $client->id,
                    'client_email' => $client->primaryContact->email,
                    'from_email' => $distributor->from_email,
                    'from_name' => $distributor->from_name,
                    'event_type' => $event_type,
                    'template_json' => json_encode($template),
                    'distributor_id' => $template->distributor_id,
                ]);

                Mail::send('emails.email', $data, function($message)use($data, $email) {
                    $message->subject($data['subject']);

                    if(isset($data['from_email']) && isset($data['from_name'])){
                        $message->from($data['from_email'], $data['from_name']);
                    }
                    $message->to($email);
                });

                $distributor->used_email += 1;
                $distributor->total_email -= 1;
                $distributor->save();
            }
        }
    }

    public static function getApiUrl($mobile_number, $msg)
    {
        $sms_setting = SmsSettings::find(1);

        $parameters = json_decode($sms_setting->parameters);

        $url = $sms_setting->api_url . "?";

        foreach($parameters as $parameter) {
            $url .= $parameter->key ."=". $parameter->value ."&";
        }

        $url .= $sms_setting->mobile_param ."=". $mobile_number . "&";
        $url .= $sms_setting->msg_param ."=". $msg;

        return $url;
    }

   public static function sendSingleSMS($mobile_no, $sms_body)
    {

        $msg = urlencode($sms_body);

        $mobile_numbers = $mobile_no;

        $url = self::getApiUrl($mobile_numbers, $msg);

        $sms = curl_init();
        curl_setopt_array($sms, array(
            // CURLOPT_URL => 'http://ui.netsms.co.in/API/SendSMS.aspx?APIkey=BFhOOeTfQtahCkESTWjBMt3Tpu&SenderID=WeCare&SMSType=2&Mobile=' . $client_number . '&MsgText=' . $msg,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));

        $result = curl_exec($sms);

        $splited = explode('|',$result);

        if(!empty($splited[0]) && $splited[0] == 'ok') {
            return true;
        } else {
            return false;
        }
    }

    function sendSMS($sms_template_id, $mobile_no = array(), $shortcode_data, $user)
    {
        //Find Template
        $smsTemplate = SmsTemplate::find($sms_template_id);
        if ($smsTemplate) {
            $body = $smsTemplate->parseContent($shortcode_data);
            $msg = urlencode($body);

            $mobile_numbers = $this->filterMobileNumbers($mobile_no);

            if (count($mobile_numbers) > 0) {
                $sms = curl_init();
                curl_setopt_array($sms, array(
                    CURLOPT_URL => 'http://ui.netsms.co.in/API/SendSMS.aspx?APIkey=BFhOOeTfQtahCkESTWjBMt3Tpu&SenderID=WeCare&SMSType=2&Mobile=' . implode(",", $mobile_numbers) . '&MsgText=' . $msg,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                ));

                $result = curl_exec($sms);

                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    function filterMobileNumbers($mobiles)
    {
        $mobiles = array_filter($mobiles, function ($m) {
            return (strlen($m) > 8);
        });
        return array_map('trim', $mobiles);
    }

    function filterEmails($emails)
    {
        $emails = array_filter($emails, function ($email) {
            return (filter_var(trim($email), FILTER_VALIDATE_EMAIL));
        });
        return array_map('trim', $emails);
    }

   public static function sendEmail($user, $template_id, $shortcodes = [], $queue = false, $minute = 1)
    {
        if (!$template_id || !$user) return false;

        //Disable for Mayank
        if($user->user_id == 8) return true;

        if ($queue) {
            $when = now()->addMinutes($minute);
            try {
                Mail::to($user)->later($when, new SendEmailViaQueue($template_id, $shortcodes));

                $log = new EmailSmsLog;
                $log->user_id = $user->user_id;
                $log->template_id = $template_id;
                $log->type = 'EMAIL';
                $log->response = null;
                $log->save();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            try {
                Mail::to($user)->send(new SendEmail($template_id, $shortcodes));

                $log = new EmailSmsLog;
                $log->user_id = $user->user_id;
                $log->template_id = $template_id;
                $log->type = 'EMAIL';
                $log->response = null;
                $log->save();
                return true;
            } catch (\Exception $e) {
                return $e;
            }
        }

        return false;
    }

    public static function shortcodes($user = false)
    {
        if (!$user) {
            return [];
        }

        $shortcodes = config()->get('shortcodes.magic_keywords');

        $keywords = [];

        $magic_keyword_keys = array_map(function ($item) {
            return $item['shortcode'];
        }, $shortcodes);

        $magic_keyword_values = [];

        $magic_keyword_values = array_map(function ($item) use ($user) {
            return $this->transformShortcodeValue($item, $user);
        }, $shortcodes);

        if (count($magic_keyword_values) == count($magic_keyword_keys)) {
            $keywords = array_combine($magic_keyword_keys, $magic_keyword_values);
        }

        return $keywords;
    }

    public static function profileProgress($u)
    {
        $progress = 0;

        $profiles = [
            'photo' => 15,
            'alt_mobileno' => 10,
            'address' => 15,
            'pincode' => 10,
            'city' => 10,
            'state_id' => 10,
            'country_id' => 10,
            'emr_contact_person' => 10,
            'emr_contact_number' => 10,
        ];

        foreach ($profiles as $column => $perc) {
            if ($u->{$column} != '') {
                $progress += $perc;
            }
        }

        return $this->decimal($progress);
    }

    public static function decimal($number, $decimal = 2)
    {
        $number = (float) $number;
        if (strpos($number, ".") === true) {
            //return sprintf('%0.2f', $number);
        }
        return round(number_format($number, $decimal));
    }

    public static function decimalNumber($nunber, $zero = 2, $dot = '.')
    {
        return number_format($nunber, $zero, $dot,'');
    }

	public static function addActionLog($user_id, $module, $module_id, $action, $old = [], $new = [])
    {
        $log = new ActionLog;
        $log->user_id = $user_id;
        $log->module = $module;
        $log->module_id = $module_id;
        $log->action = $action;
        if (!empty($old)) {
            $log->oldData = $old;
        }
        if (!empty($new)) {
            $log->newData = $new;
        }
        $log->save();
    }
	function get_client_info($cid){
		return Client::find($cid);
	}

	function getRows($file)
    {
        $replace = substr($file, 0, strpos($file, ',') + 1);
        $newFile = str_replace($replace, '', $file);
        $newFile = str_replace(' ', '+', $newFile);
        $rows = explode("\n", base64_decode($newFile));
        $array = array_map('str_getcsv', $rows);
        return $array;
    }

    function checkUserLimit($companyId)
    {
        $company = Company::find($companyId);
        $status = 1;
        if($company->plan_id)
        {
            $noOfUsers = Plan::find($company->plan_id);
            if(!$noOfUsers)
            {
                return ['status'=>2];
            }
            $noOfUsers = $noOfUsers->no_of_users;
            $usersCount = User::where('company_id',$companyId)->count();
            $status = 1;
            if(($usersCount + 1) > $noOfUsers)
            {
               $status = 0;
            }
        }
        return ['status'=>$status];
    }

    // get section model to model id wise
    function sectionModel($modelId)
    {
        $sectionModel = null;
        switch ($modelId) {
            case 'contact':
                $sectionModel = new ContactSection;
                break;
            case 'product':
                $sectionModel = new ProductSection;
                break;
            case 'employee':
                $sectionModel = new EmployeeSection;
                break;
        }
        return $sectionModel;
    }

    // get field model to model id wise
    function fieldModel($modelId)
    {
        $fieldModel = null;
        switch ($modelId) {
            case 'contact':
                $fieldModel = new ContactField;
                break;
            case 'product':
                $fieldModel = new ProductField;
                break;
            case 'employee':
                $fieldModel = new EmployeeField;
                break;
        }
        return $fieldModel;
    }

    // get field value model to model id wise
    function fieldValueModel($modelId)
    {
        $fieldValueModel = null;
        switch ($modelId) {
            case 'contact':
            $fieldValueModel = new ContactValue;
                break;
            case 'product':
            $fieldValueModel = new ProductValue;
                break;
            case 'employee':
            $fieldValueModel = new EmployeeValue;
                break;
        }
        return $fieldValueModel;
    }

    // get field value
    function getFieldValue($type,$typeId)
    {
        $fieldValue = FieldValue::where('type',$type)->where('type_id',$typeId)->pluck('values')->toArray();
        // $fieldValue = implode('<br />',$fieldValue);
        return $fieldValue;
    }

    // get primary company contact
    function getCompanyContact($companyId)
    {
        $contact = User::where('company_id',$companyId)->where('company_contact_type','1')->orderBy('updated_at','DESC')->first();
        return $contact;
    }

    // get company Data
    function getCompany($companyId){
        return Company::find($companyId);
    }

    function getPlanData($clientId,$companyId,$type){
       $plans = ClientPlan::where('client_id',$clientId)->where('company_id',$companyId);
       if($type == 1)
       {
            $plans = $plans->with('plan')->get()->pluck('plan.name')->toArray();
       }else{
            $plans = $plans->pluck('final_amount')->toArray();
       }
       $plans = implode(',',$plans);
       return $plans;
    }

    function getModelName($modelId)
    {
        $modelName = null;
        switch ($modelId) {
            case 'contact':
                $modelName = new Contacts;
                break;
            case 'product':
                $modelName = new Products;
                break;
            case 'employee':
                $modelName = new User;
                break;
        }
        return $modelName;
    }

    public static function interestedProductAssign($contactId,$leadId)
    {
        $products = [];
        $columnType = $contactId ? 'contact_id' : 'lead_id';
        $id = $contactId ? $contactId : $leadId;
        $interestedProduct = collect(InterestedProduct::where($columnType,$id)->get(['id','product_id','contact_id','lead_id']))->map(function($q) use(&$products){
            if(!empty($q->getProductData)){
                $q->getProductData->ipid = $q->id;
            }
            $products[] = $q->getProductData;
            return $products;
        });
        return $products;
    }

    public static function storeEmailHistory($templateId,$type,$typeId,$senderId,$receiverId,$receiverEmail,$ccMail=null,$bccMail=null,$subject=null,$content=null)
    {
        $emailHistory = new EmailHistory();
        $emailHistory->email_template_id = $templateId;
        $emailHistory->type  = $type;
        $emailHistory->type_id = $typeId;
        $emailHistory->sender_id = $senderId;
        $emailHistory->receiver_id = $receiverId;
        $emailHistory->receiver_email = $receiverEmail;
        $emailHistory->is_send = 0;
        $emailTemplate = EmailTemplate::find($templateId);
        if($emailTemplate)
        {
            if(!$subject){
                $subject = $emailTemplate->subject;
            }
            if(!$content){
                $content = $emailTemplate->content;
            }
            $data['messagecontent'] = $content;
            $data['subject'] = $subject;
            Mail::send('emails.email', $data, function($message) use($receiverEmail,$ccMail,$bccMail,$subject){
                $message->to($receiverEmail);
                if($ccMail){
                    $message->cc($ccMail);
                }
                if($bccMail){
                    $message->bcc($bccMail);
                }
                $message->subject($subject);
                $message->from('info@unicepts.in');
             });
             if (Mail::failures()) {
                 $emailHistory->is_send = 0;
             }else{
                $emailHistory->is_send = 1;
             }
        }
        $emailHistory->save();
        return true;
    }

    public static function getPermissionIds($companyId)
    {
        $assignedPermssion = CompanyPermission::where('company_id',$companyId)->pluck('permission_id')->toArray();
        return $assignedPermssion;
    }

    public static function getColumnStructure($tableName)
    {
        $data = [];
        if($tableName == 'leads'){
           $data['fields']['is_completed']['label'] = 'Is Completed';
           $data['fields']['is_completed']['type'] = 'boolean';
           $data['fields']['is_completed']['valueSources'] = ['value'];
           $data['fields']['is_completed']['operators'] = ['equal'];
           $data['fields']['lead_name']['label'] = 'Lead Name';
           $data['fields']['lead_name']['type'] = 'text';
           $data['fields']['lead_name']['valueSources'] = ['value'];
           $data['fields']['stage']['label'] = 'Stage';
           $data['fields']['stage']['type'] = 'select';
           $data['fields']['stage']['valueSources'] = ['value'];
           $data['fields']['stage']['fieldSettings']['listValues'] = [['value'=>'1','title'=>'Cold'],['value'=>'2','title'=>'Warm'],['value'=>'3','title'=>'Hot'],['value'=>'4','title'=>'Converted'],['value'=>'5','title'=>'Closed'],['value'=>'6','title'=>'Cros-sell/up-sell']];
           $data['fields']['lead_source']['label'] = 'Lead Source';
           $data['fields']['lead_source']['type'] = 'text';
           $data['fields']['lead_source']['valueSources'] = ['value'];
           $data['fields']['company_name']['label'] = 'Company Name';
           $data['fields']['company_name']['type'] = 'text';
           $data['fields']['company_name']['valueSources'] = ['value'];
           $data['fields']['customer_name']['label'] = 'Customer Name';
           $data['fields']['customer_name']['type'] = 'text';
           $data['fields']['customer_name']['valueSources'] = ['value'];
           $data['fields']['email']['label'] = 'Email';
           $data['fields']['email']['type'] = 'text';
           $data['fields']['email']['valueSources'] = ['value'];
           $data['fields']['cc_email']['label'] = 'CC Email';
           $data['fields']['cc_email']['type'] = 'text';
           $data['fields']['cc_email']['valueSources'] = ['value'];
           $data['fields']['bcc_email']['label'] = 'BCC Email';
           $data['fields']['bcc_email']['type'] = 'text';
           $data['fields']['bcc_email']['valueSources'] = ['value'];
           $data['fields']['secondary_email']['label'] = 'Secondary Email';
           $data['fields']['secondary_email']['type'] = 'text';
           $data['fields']['secondary_email']['valueSources'] = ['value'];
           $data['fields']['mobile_no']['label'] = 'Mobile Number';
           $data['fields']['mobile_no']['type'] = 'number';
           $data['fields']['mobile_no']['fieldSettings']['min'] = '10';
           $data['fields']['mobile_no']['fieldSettings']['max'] = '10';
           $data['fields']['mobile_no']['valueSources'] = ['value'];
           $data['fields']['established_in']['label'] = 'Established Iin';
           $data['fields']['established_in']['type'] = 'select';
           $data['fields']['established_in']['valueSources'] = ['value'];
           $data['fields']['turnover']['label'] = 'Turnover';
           $data['fields']['turnover']['type'] = 'number';
           $data['fields']['turnover']['valueSources'] = ['value'];
           $data['fields']['gst_no']['label'] = 'GST Number';
           $data['fields']['gst_no']['type'] = 'number';
           $data['fields']['gst_no']['valueSources'] = ['value'];
           $data['fields']['pan_no']['label'] = 'Pan Number';
           $data['fields']['pan_no']['type'] = 'number';
           $data['fields']['pan_no']['valueSources'] = ['value'];
           $data['fields']['no_of_employees']['label'] = 'No Of Employees';
           $data['fields']['no_of_employees']['type'] = 'number';
           $data['fields']['no_of_employees']['valueSources'] = ['value'];
           $data['fields']['website']['label'] = 'Website';
           $data['fields']['website']['type'] = 'text';
           $data['fields']['website']['valueSources'] = ['value'];
           $data['fields']['city']['label'] = 'City';
           $data['fields']['city']['type'] = 'text';
           $data['fields']['city']['valueSources'] = ['value'];
           $data['fields']['state']['label'] = 'State';
           $data['fields']['state']['type'] = 'select';
           $data['fields']['state']['valueSources'] = ['value'];
           $data['fields']['country']['label'] = 'Country';
           $data['fields']['country']['type'] = 'select';
           $data['fields']['country']['valueSources'] = ['value'];
           $data['fields']['postcode']['label'] = 'Postcode';
           $data['fields']['postcode']['type'] = 'text';
           $data['fields']['postcode']['valueSources'] = ['value'];
           $data['fields']['company_type']['label'] = 'Company Type';
           $data['fields']['company_type']['type'] = 'select';
           $data['fields']['company_type']['valueSources'] = ['value'];
           $data['fields']['industry']['label'] = 'Industry';
           $data['fields']['industry']['type'] = 'select';
           $data['fields']['industry']['valueSources'] = ['value'];
        }
        if($tableName == 'contacts'){
           $data['fields']['name']['label'] = 'Name';
           $data['fields']['name']['type'] = 'text';
           $data['fields']['name']['valueSources'] = ['value'];
           $data['fields']['company_name']['label'] = 'Company Name';
           $data['fields']['company_name']['type'] = 'text';
           $data['fields']['company_name']['valueSources'] = ['value'];
           $data['fields']['email']['label'] = 'Email';
           $data['fields']['email']['type'] = 'text';
           $data['fields']['email']['valueSources'] = ['value'];
           $data['fields']['cc_email']['label'] = 'CC Email';
           $data['fields']['cc_email']['type'] = 'text';
           $data['fields']['cc_email']['valueSources'] = ['value'];
           $data['fields']['bcc_email']['label'] = 'BCC Email';
           $data['fields']['bcc_email']['type'] = 'text';
           $data['fields']['bcc_email']['valueSources'] = ['value'];
           $data['fields']['secondary_email']['label'] = 'Secondary Email';
           $data['fields']['secondary_email']['type'] = 'text';
           $data['fields']['secondary_email']['valueSources'] = ['value'];
           $data['fields']['mobile_no']['label'] = 'Mobile Number';
           $data['fields']['mobile_no']['type'] = 'number';
           $data['fields']['mobile_no']['valueSources'] = ['value'];
           $data['fields']['mobile_no']['fieldSettings']['min'] = '10';
           $data['fields']['mobile_no']['fieldSettings']['max'] = '10';
           $data['fields']['established_in']['label'] = 'Established Iin';
           $data['fields']['established_in']['type'] = 'select';
           $data['fields']['established_in']['valueSources'] = ['value'];
           $data['fields']['turnover']['label'] = 'Turnover';
           $data['fields']['turnover']['type'] = 'number';
           $data['fields']['turnover']['valueSources'] = ['value'];
           $data['fields']['gst_no']['label'] = 'GST Number';
           $data['fields']['gst_no']['type'] = 'number';
           $data['fields']['gst_no']['valueSources'] = ['value'];
           $data['fields']['pan_no']['label'] = 'Pan Number';
           $data['fields']['pan_no']['type'] = 'number';
           $data['fields']['pan_no']['valueSources'] = ['value'];
           $data['fields']['no_of_employees']['label'] = 'No Of Employees';
           $data['fields']['no_of_employees']['type'] = 'number';
           $data['fields']['no_of_employees']['valueSources'] = ['value'];
           $data['fields']['website']['label'] = 'Website';
           $data['fields']['website']['type'] = 'text';
           $data['fields']['website']['valueSources'] = ['value'];
           $data['fields']['city']['label'] = 'City';
           $data['fields']['city']['type'] = 'text';
           $data['fields']['city']['valueSources'] = ['value'];
           $data['fields']['state']['label'] = 'State';
           $data['fields']['state']['type'] = 'select';
           $data['fields']['state']['valueSources'] = ['value'];
           $data['fields']['country']['label'] = 'Country';
           $data['fields']['country']['type'] = 'select';
           $data['fields']['country']['valueSources'] = ['value'];
           $data['fields']['postcode']['label'] = 'Postcode';
           $data['fields']['postcode']['type'] = 'text';
           $data['fields']['postcode']['valueSources'] = ['value'];
           $data['fields']['company_type']['label'] = 'Company Type';
           $data['fields']['company_type']['type'] = 'select';
           $data['fields']['company_type']['valueSources'] = ['value'];
           $data['fields']['industry']['label'] = 'Industry';
           $data['fields']['industry']['type'] = 'select';
           $data['fields']['industry']['valueSources'] = ['value'];
        }

        return $data;
    }

    public static function usersType($type=null)
    {
        $type_array = array(
            1 => 'Admin',
            2 => 'Normal',
            3 => 'Dealer',
            4 => 'Distributor'
        );

        if($type){
           return $type_array[$type];
        }else{
            return $type_array;
        }
    }

    public static function emailTemplateCss()
    {
        return '/* -------------------------------------
                    GLOBAL RESETS
                ------------------------------------- */

                /*All the styling goes here*/

                img {
                border: none;
                -ms-interpolation-mode: bicubic;
                max-width: 100%;
                }

                body {
                background-color: #f6f6f6;
                font-family: sans-serif;
                -webkit-font-smoothing: antialiased;
                font-size: 14px;
                line-height: 1.4;
                margin: 0;
                padding: 0;
                -ms-text-size-adjust: 100%;
                -webkit-text-size-adjust: 100%;
                }

                table {
                border-collapse: separate;
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                border: none !important;
                width: 100%; }
                table td {
                    font-family: sans-serif;
                    font-size: 14px;
                    vertical-align: top;
                }

                /* -------------------------------------
                    BODY & CONTAINER
                ------------------------------------- */

                .body {
                background-color: #f6f6f6;
                width: 100%;
                }

                /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
                .container {
                display: block;
                margin: 0 auto !important;
                /* makes it centered */
                max-width: 580px;
                padding: 10px;
                }

                /* This should also be a block element, so that it will fill 100% of the .container */
                .content {
                box-sizing: border-box;
                display: block;
                margin: 0 auto;
                max-width: 580px;
                padding: 10px;
                }

                /* -------------------------------------
                    HEADER, FOOTER, MAIN
                ------------------------------------- */
                .main {
                background: #ffffff;
                border-radius: 3px;
                width: 100%;
                }

                .wrapper {
                box-sizing: border-box;
                padding: 20px;
                }

                .content-block {
                padding-bottom: 10px;
                padding-top: 10px;
                }

                .footer {
                clear: both;
                margin-top: 10px;
                text-align: center;
                width: 100%;
                }
                .footer td,
                .footer p,
                .footer span,
                .footer a {
                    color: #999999;
                    font-size: 12px;
                    text-align: center;
                }

                /* -------------------------------------
                    TYPOGRAPHY
                ------------------------------------- */
                h1,
                h2,
                h3,
                h4 {
                color: #000000;
                font-family: sans-serif;
                font-weight: 400;
                line-height: 1.4;
                margin: 0;
                margin-bottom: 30px;
                }

                h1 {
                font-size: 35px;
                font-weight: 300;
                text-align: center;
                text-transform: capitalize;
                }

                p,
                ul,
                ol {
                font-family: sans-serif;
                font-size: 14px;
                font-weight: normal;
                margin: 0;
                margin-bottom: 15px;
                }
                p li,
                ul li,
                ol li {
                    list-style-position: inside;
                    margin-left: 5px;
                }

                a {
                color: #3498db;
                text-decoration: underline;
                }

                /* -------------------------------------
                    BUTTONS
                ------------------------------------- */
                .btn {
                box-sizing: border-box;
                width: 100%; }
                .btn > tbody > tr > td {
                    padding-bottom: 15px; }
                .btn table {
                    width: auto;
                }
                .btn table td {
                    background-color: #ffffff;
                    border-radius: 5px;
                    text-align: center;
                }
                .btn a {
                    background-color: #ffffff;
                    border: solid 1px #3498db;
                    border-radius: 5px;
                    box-sizing: border-box;
                    color: #3498db;
                    cursor: pointer;
                    display: inline-block;
                    font-size: 14px;
                    font-weight: bold;
                    margin: 0;
                    padding: 12px 25px;
                    text-decoration: none;
                    text-transform: capitalize;
                }

                .btn-primary table td {
                background-color: #3498db;
                }

                .btn-primary a {
                background-color: #3498db;
                border-color: #3498db;
                color: #ffffff;
                }

                /* -------------------------------------
                    OTHER STYLES THAT MIGHT BE USEFUL
                ------------------------------------- */
                .last {
                margin-bottom: 0;
                }

                .first {
                margin-top: 0;
                }

                .align-center {
                text-align: center;
                }

                .align-right {
                text-align: right;
                }

                .align-left {
                text-align: left;
                }

                .clear {
                clear: both;
                }

                .mt0 {
                margin-top: 0;
                }

                .mb0 {
                margin-bottom: 0;
                }

                .powered-by a {
                text-decoration: none;
                }

                hr {
                border: 0;
                border-bottom: 1px solid #f6f6f6;
                margin: 20px 0;
                }

                /* -------------------------------------
                    RESPONSIVE AND MOBILE FRIENDLY STYLES
                ------------------------------------- */
            @media only screen and (max-width: 620px) {
                table[class=body] h1 {
                    font-size: 28px !important;
                    margin-bottom: 10px !important;
                }
                table[class=body] p,
                table[class=body] ul,
                table[class=body] ol,
                table[class=body] td,
                table[class=body] span,
                table[class=body] a {
                    font-size: 16px !important;
                }
                table[class=body] .wrapper,
                table[class=body] .article {
                    padding: 10px !important;
                }
                table[class=body] .content {
                    padding: 0 !important;
                }
                table[class=body] .container {
                    padding: 0 !important;
                    width: 100% !important;
                }
                table[class=body] .main {
                    border-left-width: 0 !important;
                    border-radius: 0 !important;
                    border-right-width: 0 !important;
                }
                table[class=body] .btn table {
                    width: 100% !important;
                }
                table[class=body] .btn a {
                    width: 100% !important;
                }
                table[class=body] .img-responsive {
                    height: auto !important;
                    max-width: 100% !important;
                    width: auto !important;
                }
            }

                /* -------------------------------------
                    PRESERVE THESE STYLES IN THE HEAD
                ------------------------------------- */
            @media all {
                .ExternalClass {
                    width: 100%;
                }
                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                    line-height: 100%;
                }
                .apple-link a {
                    color: inherit !important;
                    font-family: inherit !important;
                    font-size: inherit !important;
                    font-weight: inherit !important;
                    line-height: inherit !important;
                    text-decoration: none !important;
                }
                #MessageViewBody a {
                    color: inherit;
                    text-decoration: none;
                    font-size: inherit;
                    font-family: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                }
                .btn-primary table td:hover {
                    background-color: #34495e !important;
                }
                .btn-primary a:hover {
                    background-color: #34495e !important;
                    border-color: #34495e !important;
                }
            }';
    }

    public static function emailTemplateBody()
    {
        return '<!doctype html>
                    <html>
                    <head>
                        <meta name="viewport" content="width=device-width" />
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <title>Email</title>
                        <style>
                        {{#all_css}}
                        </style>
                    </head>
                    <body>
                        {{#template_body}}
                    </body>
                </html>';
    }


    public static function defaultInvoiceTemplate(){

            return '<div class="invoice-box">
                <table cellpadding="0" cellspacing="0" border="1">
                    <tr class="top">
                        <td colspan="2">
                            <h1>Invoice</h1>
                        </td>
                    </tr>
                    <tr class="top">
                        <td colspan="2">
                            <table width="100%">
                                <tr>
                                    <td class="title">
                                        {{#company_name}}
                                    </td>
                                    <td>
                                        Invoice : #{{#invoice_no}}<br />
                                        Order Date : {{#created_date}}<br/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr class="information">
                        <td colspan="2">
                            <table>
                                <tr>
                                    <td>
                                        {{#company_address}}
                                        <br/>
                                        GST Number : {{#gst_no}}
                                    </td>
                                    <td>
                                        {{#client_name}}<br/>
                                        {{#client_email}}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr class="heading">
                        <td colspan="2">
                            <table cellpadding="0" cellspacing="0" class="details_table">
                                <tr>
                                    <th width="15%">Product/Service</th>
                                    <th width="15%">QTY</th>
                                    <th width="15%">Amount</th>
                                    <th width="10%">Deal Discount %</th>
                                    <th width="10%">Discount %</th>
                                    <th width="15%">Discount Amount</th>
                                    <th width="20%">Final Amount</th>
                                </tr>
                                <tr id="plan_list" class="plan_item">
                                    <td colspan="6"></td>
                                </tr>
                                <tfoot></tfoot>
                                <tr id="discount_code_tr">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">Discount Code</th>
                                    <th class="text_right">{{#discount_code}}</th>
                                </tr>
                                <tr id="sgst">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">SGST</th>
                                    <th class="text_right">{{#sgst_amount}}</th>
                                </tr>
                                <tr id="cgst">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">CGST</th>
                                    <th class="text_right">{{#cgst_amount}}</th>
                                </tr>
                                <tr id="igst">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">IGST</th>
                                    <th class="text_right">{{#igst_amount}}</th>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">Total Amount</th>
                                    <th class="text_right">{{#total_amount}}</th>
                                </tr>
                                <tr id="payment_pending">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">Payment Pending</th>
                                    <th class="text_right">{{#yes_no}}</th>
                                </tr>
                                <tr id="payment_mode">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">Payment Mode</th>
                                    <th class="text_right">{{#payment_mode}}</th>
                                </tr>
                                <tr id="payment_date">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">Payment Date</th>
                                    <th class="text_right">{{#payment_date}}</th>
                                </tr>
                                <tr id="bank_name_row">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">Bank Name</th>
                                    <th class="text_right">{{#bank_name}}</th>
                                </tr>
                                <tr id="tansaction_number">
                                    <td colspan="4"></td>
                                    <th class="text_left" colspan="2">Transaction Number</th>
                                    <th class="text_right">{{#transaction_no}}</th>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>';
    }

    public static function defaultSubscriptionTemplate(){

        return '<div class="invoice-box">
            <table cellpadding="0" cellspacing="0" border="1">
                <tr class="top">
                    <td colspan="2">
                        <h1>Invoice</h1>
                    </td>
                </tr>
                <tr class="top">
                    <td colspan="2">
                        <table width="100%">
                            <tr>
                                <td class="title">
                                    {{#salon_name}}
                                </td>
                                <td>
                                    Invoice : #{{#invoice_no}}<br />
                                    Order Date : {{#created_date}}<br/>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="2">
                        <table>
                            <tr>
                                <td>
                                    {{#salon_address}}
                                    <br/>
                                    GST Number : {{#gst_no}}
                                </td>
                                <td>
                                    {{#salon_name}}<br/>
                                    {{#salon_email}}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr class="heading">
                    <td colspan="2">
                        <table cellpadding="0" cellspacing="0" class="details_table">
                            <tr>
                                <th>Plan</th>
                                <th width="15%">Plan Date</th>
                                <th width="5%">Amount</th>
                                <th width="10%">Discount %</th>
                                <th width="15%">Discount Amount</th>
                                <th width="20%">Final Amount</th>
                            </tr>
                            <tr id="plan_list" class="plan_item">
                                <td colspan="6"></td>
                            </tr>
                            <tfoot></tfoot>
                            <tr id="sgst">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">SGST</th>
                                <th class="text_right">{{#sgst_amount}}</th>
                            </tr>
                            <tr id="cgst">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">CGST</th>
                                <th class="text_right">{{#cgst_amount}}</th>
                            </tr>
                            <tr id="igst">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">IGST</th>
                                <th class="text_right">{{#igst_amount}}</th>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">Total Amount</th>
                                <th class="text_right">{{#total_amount}}</th>
                            </tr>
                            <tr id="payment_pending">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">Payment Pending</th>
                                <th class="text_right">{{#yes_no}}</th>
                            </tr>
                            <tr id="payment_mode">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">Payment Mode</th>
                                <th class="text_right">{{#payment_mode}}</th>
                            </tr>
                            <tr id="payment_date">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">Payment Date</th>
                                <th class="text_right">{{#payment_date}}</th>
                            </tr>
                            <tr id="bank_name_row">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">Bank Name</th>
                                <th class="text_right">{{#bank_name}}</th>
                            </tr>
                            <tr id="tansaction_number">
                                <td colspan="3"></td>
                                <th class="text_left" colspan="2">Transaction Number</th>
                                <th class="text_right">{{#transaction_no}}</th>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>';
    }

    public static function invoiceTemplateBody(){

        return '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>Invoice</title>
                <style>
                    .invoice-box {
                        max-width: 100%;
                        margin: auto;
                        border: 1px solid #eee;
                        font-size: 16px;
                        line-height: 24px;
                        color: #555;
                    }
                    .invoice-box table {
                        width: 100%;
                        line-height: inherit;
                        text-align: left;
                        border: 1px solid #b9b9b9;
                    }
                    .invoice-box table td {
                        padding: 0px;
                        vertical-align: top;
                    }
                    .invoice-box table table td {
                        padding: 10px;
                    }
                    .invoice-box table tr td:nth-child(2) {
                        text-align: right;
                    }
                    .invoice-box table tr.top table td.title {
                        font-size: 30px;
                        line-height: 60px;
                        color: #333;
                    }
                    .invoice-box table tr.top h1{
                        text-align:center;
                        color: #333;
                    }
                    .invoice-box table tr.information table {
                        padding: 0;
                        border: 0;
                    }
                    .invoice-box table tr.information table td:nth-child(1) {
                        border-right: 1px solid #ddd;
                    }
                    .invoice-box table td.title{
                        border-right: 1px solid #ddd;
                    }
                    .invoice-box table tr.top table {
                        padding: 0;
                        border: 0;
                    }
                    .invoice-box table tr.heading td,
                    .invoice-box table tr.heading th {
                        background: #eee;
                        border: 0;
                    }
                    .invoice-box table tr.details td {
                        padding-bottom: 20px;
                    }
                    .invoice-box table table tr.item td {
                        border: none !important;
                        background: #fff !important;
                    }
                    .invoice-box table tr.item.last td {
                        border-bottom: none;
                    }
                    .invoice-box table tr.total td:nth-child(2) {
                        border-top: 2px solid #eee;
                        font-weight: bold;
                    }
                    .invoice-box .details_table{
                        border: none;
                    }
                    .invoice-box .details_table td,
                    .invoice-box .details_table th {
                        border: 1px solid #b9b9b9 !important;
                        padding: 10px;
                    }
                    .invoice-box .details_table tbody th{
                        border-top: none !important;
                    }
                    .text_left{
                        text-align: left !important;
                    }
                    .text_right{
                        text-align: right !important;
                    }
                    .plan_item td{
                        background: #fff !important;
                    }
                    @media only screen and (max-width: 600px) {
                        .invoice-box table tr.top table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                        .invoice-box table tr.information table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                    }
                    {{#server_css}}
                </style>
            </head>
            <body>
                {{#template_content}}
            </body>
        </html>';
    }

    public static function reportTemplate(){

        return '<!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8" />
                <title>{{#title}}</title>
                <style>
                    * {
                        font-family: Arial;
                        font-size: 14 px;
                    }
                    .invoice-box {
                        max-width: 100%;
                        margin: auto;
                        border: 1px solid #eee;
                        font-size: 14px;
                        line-height: 24px;
                        color: #555;
                    }
                    .invoice-box table {
                        width: 100%;
                        line-height: inherit;
                        text-align: left;
                    }
                    .invoice-box table th {
                        border: 1px solid #b9b9b9;
                    }
                    .invoice-box table td {
                        border: 1px solid #b9b9b9;
                        padding: 0px;
                        vertical-align: top;
                    }
                    .invoice-box table table td {
                        padding: 10px;
                    }
                    .invoice-box table tr td:nth-child(2) {
                        text-align: right;
                    }
                    .invoice-box table tr.top table td.title {
                        font-size: 30px;
                        line-height: 60px;
                        color: #333;
                    }
                    .invoice-box table tr.top h1{
                        text-align:center;
                        color: #333;
                    }
                    .invoice-box table tr.information table {
                        padding: 0;
                        border: 0;
                    }
                    .invoice-box table tr.information table td:nth-child(1) {
                        border-right: 1px solid #ddd;
                    }
                    .invoice-box table td.title{
                        border-right: 1px solid #ddd;
                    }
                    .invoice-box table tr.top table {
                        padding: 0;
                        border: 0;
                    }
                    .invoice-box table tr.heading td,
                    .invoice-box table tr.heading th {
                        background: #eee;
                        border: 0;
                    }
                    .invoice-box table tr.details td {
                        padding-bottom: 20px;
                    }
                    .invoice-box table table tr.item td {
                        border: none !important;
                        background: #fff !important;
                    }
                    .invoice-box table tr.item.last td {
                        border-bottom: none;
                    }
                    .invoice-box table tr.total td:nth-child(2) {
                        border-top: 2px solid #eee;
                        font-weight: bold;
                    }
                    .invoice-box .details_table{
                        border: none;
                    }
                    .invoice-box .details_table td,
                    .invoice-box .details_table th {
                        border: 1px solid #b9b9b9 !important;
                        padding: 10px;
                    }
                    .invoice-box .details_table tbody th{
                        border-top: none !important;
                    }
                    .text_left{
                        text-align: left !important;
                    }
                    .text_right{
                        text-align: right !important;
                    }
                    .plan_item td{
                        background: #fff !important;
                    }
                    @media only screen and (max-width: 600px) {
                        .invoice-box table tr.top table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                        .invoice-box table tr.information table td {
                            width: 100%;
                            display: block;
                            text-align: center;
                        }
                    }
                    {{#server_css}}
                </style>
            </head>
            <body>
                <div class="invoice-box">
                    {{#template_content}}
                </div>
            </body>
        </html>';
    }

    public static function reportErrorTemplate()
    {
        return '<table style="width: 100%;" cellpadding="0" cellspacing="0">
                    <tr>
                        <th style="text-aling:center;font-size:16px">{{#error_message}}</th>
                    </tr>
                </table>';
    }

    public static function reportTable()
    {
        return '<table class="table table-striped table-bordered" id="report-table" style="width:100%">
                    <thead>
                        <tr id="table-head">{{#thead_content}}</tr>
                    </thead>
                    <tbody id="report-body">{{#tbody_content}}</tbody>
                </table>';
    }

    public static function unitList($id = null){

        $list = array(
            1 => 'Bags',
            2 => 'Bale',
            3 => 'Bundles',
            4 => 'Buckles',
            5 => 'Billions of units',
            6 => 'Box',
            7 => 'Bottles',
            8 => 'Bunches',
            9 => 'Cans',
            10 => 'Cubic meter',
            11 => 'Cubic centimeter',
            12 => 'Centimeter',
            13 => 'Cartons',
            14 => 'Dozen',
            15 => 'Drum',
            16 => 'Great gross',
            17 => 'Grams',
            18 => 'Gross',
            19 => 'Gross yards',
            20 => 'Kilograms',
            21 => 'Kiloliter',
            22 => 'Kilometre',
            23 => 'Millilitre',
            24 => 'Meters',
            25 => 'Metric',
            26 => 'Numbers',
            27 => 'Packs',
            28 => 'Pieces',
            29 => 'Pairs',
            30 => 'Quintal',
            31 => 'Rolls',
            32 => 'Set',
            33 => 'Square feet',
            34 => 'Square meters',
            35 => 'Square yards',
            36 => 'Tablets',
            37 => 'Ten gross',
            38 => 'Thousands',
            39 => 'Tonnes',
            40 => 'Tubes',
            41 => 'Us gallons',
            42 => 'Unit',
            43 => 'Yards',
            44 => 'Others'
        );

        if($id){
            if(array_key_exists($id, $list)){
                return $list[$id];
            }else{
                return null;
            }
         }else{
             return $list;
         }
    }

    // For Deals & Discounts
    public static function is_weekend()
    {
        return in_array(date("l"), ["Saturday", "Sunday"]);
    }

    public static function is_weekday($weekday, $date = false)
    {
        if($weekday == "All") {
            return true;
        }

        $date = date('Y-m-d');
        if($date != false) {
            $date = date('Y-m-d', strtotime($date));
        }

        $day_name = date("l", strtotime($date));

        return strtoupper($day_name) == strtoupper($weekday);
    }

    public static function is_holiday($date = false, $distributor_id)
    {
        $date = date('Y-m-d');
        if($date != false) {
            $date = date('Y-m-d', strtotime($date));
        }

        $holiday = Holiday::where('date', $date)->where('distributor_id', $distributor_id)->count();

        if($holiday > 0)  {
            return true;
        } else {
            return false;
        }
    }

    public static function is_event($client_id, $date, $field, $distributor_id)
    {
        $date = date('Y-m-d');
        if($date != false) {
            $date = date('Y-m-d', strtotime($date));
        }

        $client = Client::where('id', $client_id)
        ->whereRaw("DAYOFMONTH($field) =?",  date('d', strtotime($date)))
        ->whereRaw("MONTH($field) =?", date('m', strtotime($date)))
        ->where('distributor_id', $distributor_id)->count();
        if($client > 0)  {
            return true;
        } else {
            return false;
        }
    }

    // Replace sms variables with original values
    public static function getMessageBody($message, $client, $appointment = false)
    {
        // Check if appointment is set
        if($appointment !== false){
            // Services
            $services = $appointment->services->pluck('name')->toArray();
            $data['services'] = implode(', ',$services);

            // Representative Name
            $representative = isset($appointment->user->first_name) ? $appointment->user->first_name . " " : '';
            $representative .= isset($appointment->user->last_name) ? $appointment->user->last_name : "";
            $data['representative'] = $representative;

            // Appoinment Date, time
            $data['appointment_date'] = date('d-m-Y', strtotime($appointment->date));
            $data['appointment_start_at'] = date('h:i a', strtotime($appointment->start_at));
            $data['appointment_end_at'] = date('h:i a', strtotime($appointment->end_at));
        }

        $message_variable = array(
            '{{#client_name}}' => $client->name,
            '{{#client_email}}' =>  $client->primaryContact->email,
            '{{#client_contact_number}}' =>  $client->primaryContact->primary_number,
            '{{#client_whatsapp_number}}' =>  $client->primaryContact->secondary_number,
            '{{#appointment_for}}' => ($appointment !== false ? $data['services'] : ""),
            '{{#appointment_representative}}' => ($appointment !== false ? $data['representative'] : ""),
            '{{#appointment_date}}' => ($appointment !== false ? $data['appointment_date'] : ""),
            '{{#appointment_start_time}}' => ($appointment !== false ? $data['appointment_start_at'] : ""),
            '{{#appointment_end_time}}' => ($appointment !== false ? $data['appointment_end_at'] : ""),
        );

        foreach($message_variable as $key => $value) {
            $message = str_replace($key, $value, $message);
        }
        return $message;
    }
}
