<!doctype html>
<html>
  <head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>{{ $subject }}</title>
    <style>
      img {
        border: none;
        -ms-interpolation-mode: bicubic;
        max-width: 100%;
      }

      body {
        margin: 0 !important;
        padding: 0;
        background-color: #ffffff !important;
        font-family: Arial, sans-serif, 'Open Sans';
        font-size:13px;
      }

      .container{
        margin: 5px auto;
        max-width: 650px;
        border: 1px solid #cfcfcf;
        padding: 10px;
      }

      table, td, th {
        border: 1px solid black;
        padding:5px;
      }

      table {
        border-collapse: collapse;
        width: 100%;
      }

      .header{
          text-align:center;
      }

      .header img{
        margin:0px auto;
      }
      #spacer{min-height:30px;}

      #footer table{
         border-collapse: collapse;
         width: 100%;
      }
      #footer table, td, th {
        border: 0px solid black !important;
        padding:3px;
        font-size: 11px;
      }

      #emailcontent p{
          font-size:13px !important;
      }
    </style>
  </head>
<body>
  <div class="container">
    <div class="header">
        @php
            $logo1 = Setting::get('SITE_LOGO');
            $logo2 = Setting::get('SITE_LOGO1');
        @endphp
        <a href="{{ route('home') }}">
            <img src="{{ asset(Storage::url($logo1)) }}" style="max-height: 120px;" alt="{{ Setting::get('SITE_NAME') }}">
            <img src="{{ asset(Storage::url($logo2)) }}" style="max-height: 120px;" alt="{{ Setting::get('SITE_NAME') }}">
        </a>
    </div>
    <div id="spacer"></div>
    <div id="emailcontent">
      {!! $messagecontent !!}
      <br>
      <p><strong>UWSB</strong><br>ADMISSIONS TEAM</p>
    </div>
    <div id="footer">
    <table cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <td style="vertical-align:middle">
          <table cellspacing="0">
            <tbody>
              <tr>
                <td style="vertical-align:top;width:110px;padding: 0px;">
                  <img alt="Unitedworld School of Business" src="{{ asset(Storage::url($logo2)) }}" style="height:115px; margin-left:0px; margin-top:0px; width:110px" />
                </td>
                <td style="vertical-align:top;padding-left:10px;">
                <table cellspacing="0">
                  <tbody>
                    <tr>
                      <td style="vertical-align:middle">UWSB ADMISSION TEAM</td>
                    </tr>
                    <tr>
                      <td style="vertical-align:middle">Unitedworld School of Business | Karnavati University</td>
                    </tr>
                    <tr>
                      <td style="vertical-align:middle">Mobile:&nbsp;<a href="tel:917574811137">+91 75748 11137</a></td>
                    </tr>
                    <tr>
                      <td style="vertical-align:middle">Site:&nbsp;<a href="https://karnavatiuniversity.edu.in/">https://karnavatiuniversity.edu.in</a></td>
                    </tr>
                    <tr>
                      <td style="vertical-align:middle">Address:&nbsp;Ahmedabad: A/907, Uvarsad-Vavol Rd, Knowledge Village, Gandhinagar, Gujarat, Pincode - 382422</td>
                    </tr>
                  </tbody>
                </table>
                </td>
                <td style="vertical-align:middle;width:7%;padding: 0px;text-align: right;">
                <table cellspacing="0">
                  <tbody>
                    <tr>
                      <td style="vertical-align:middle">
                        <a href="https://www.facebook.com/UnitedworldIndia" target="_blank"><img src="{{ asset('assets/front/icon/facebook.png') }}" style="height:25px; margin-left:0px; margin-top:0px; width:25px" /></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="vertical-align:middle">
                        <a href="https://www.instagram.com/uwsb_india/" target="_blank"><img src="{{ asset('assets/front/icon/instagram.png') }}" style="height:25px; margin-left:0px; margin-top:0px; width:25px" /></a>
                      </td>
                    </tr>
                    <tr>
                      <td style="vertical-align:middle">
                        <a href="https://www.youtube.com/channel/UCUBoW3XevjEXzFdr5fYzPaA" target="_blank"><img src="{{ asset('assets/front/icon/youtube.png') }}" style="height:25px; margin-left:0px; margin-top:0px; width:25px" /></a>
                      </td>
                    </tr>
                  </tbody>
                </table>
                </td>
              </tr>
            </tbody>
          </table>
          </td>
        </tr>
      </tbody>
    </table>
    </div>
  </div>
</body>
</html>
