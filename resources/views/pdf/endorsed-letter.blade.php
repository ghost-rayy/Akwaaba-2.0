<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Endorsement Letter</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12pt; line-height: 1.6; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 15px; }
        .header h1 { font-size: 16pt; margin: 0 0 5px 0; }
        .header p { margin: 2px 0; font-size: 10pt; }
        .ref { margin-bottom: 20px; }
        .ref td { padding: 2px 10px 2px 0; font-size: 11pt; }
        .subject { text-align: center; font-weight: bold; font-size: 13pt; margin: 20px 0; text-decoration: underline; }
        .body-text { margin: 20px 0; text-align: justify; }
        .details { margin: 20px 0; }
        .details td { padding: 4px 15px 4px 0; font-size: 11pt; }
        .details td:first-child { font-weight: bold; width: 180px; }
        .signature { margin-top: 50px; }
        .signature td { padding: 4px 0; }
        .footer { margin-top: 30px; font-size: 10pt; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; color: #666; }
        .stamp-placeholder { width: 100px; height: 100px; border: 2px dashed #999; text-align: center; vertical-align: middle; color: #999; font-size: 9pt; }
        .signature-img { max-height: 60px; }
        .stamp-img { max-width: 100px; max-height: 100px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $data['company_name'] }}</h1>
        <p>{{ $data['company_location'] }}</p>
    </div>

    <table class="ref">
        <tr><td><strong>NSS Number:</strong></td><td>{{ $data['nss_number'] }}</td></tr>
        <tr><td><strong>Date:</strong></td><td>{{ $data['posting_date'] }}</td></tr>
    </table>

    <div class="subject">
        LETTER OF ENDORSEMENT FOR NATIONAL SERVICE
    </div>

    <div class="body-text">
        <p>To Whom It May Concern,</p>
        <p>
            This is to certify that <strong>{{ $data['full_name'] }}</strong> 
            (NSS Number: <strong>{{ $data['nss_number'] }}</strong>) has been duly posted to 
            <strong>{{ $data['company_name'] }}</strong> for the mandatory one-year National Service.
        </p>
        <p>The following are the details of the personnel:</p>
    </div>

    <table class="details">
        <tr><td>Full Name:</td><td>{{ $data['full_name'] }}</td></tr>
        <tr><td>NSS Number:</td><td>{{ $data['nss_number'] }}</td></tr>
        <tr><td>Date of Birth:</td><td>{{ $data['date_of_birth'] }}</td></tr>
        <tr><td>Place of Residence:</td><td>{{ $data['place_of_residence'] }}</td></tr>
        <tr><td>Region:</td><td>{{ $data['region_of_residence'] }}</td></tr>
        <tr><td>University:</td><td>{{ $data['university'] }}</td></tr>
        <tr><td>Programme:</td><td>{{ $data['programme_of_study'] }}</td></tr>
        <tr><td>Form of Education:</td><td>{{ $data['form_of_education'] }}</td></tr>
        <tr><td>Posting Date:</td><td>{{ $data['posting_date'] }}</td></tr>
        <tr><td>Start Date:</td><td>{{ $data['start_date'] }}</td></tr>
        <tr><td>End Date:</td><td>{{ $data['end_date'] }}</td></tr>
    </table>

    <div class="body-text">
        <p>
            The management of <strong>{{ $data['company_name'] }}</strong> hereby endorses 
            <strong>{{ $data['full_name'] }}</strong> for the National Service programme effective 
            {{ $data['start_date'] }} to {{ $data['end_date'] }}.
        </p>
        <p>We kindly request your favourable consideration and confirmation of this posting.</p>
    </div>

    <table class="signature">
        <tr><td><strong>Endorsed by:</strong></td></tr>
        @if ($signatureBase64)
        <tr><td><img src="{{ $signatureBase64 }}" class="signature-img" alt="Signature"></td></tr>
        @else
        <tr><td style="height: 60px;">&nbsp;</td></tr>
        @endif
        <tr><td>_________________________</td></tr>
        <tr><td><strong>Authorized Signatory</strong></td></tr>
        <tr><td>{{ $data['company_name'] }}</td></tr>
        @if ($stampBase64)
        <tr><td><img src="{{ $stampBase64 }}" class="stamp-img" alt="Stamp"></td></tr>
        @endif
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
        <p>{{ $data['company_name'] }} &bull; {{ $data['company_location'] }}</p>
    </div>
</body>
</html>
