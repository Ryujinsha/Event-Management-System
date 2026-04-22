<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<title>Certificate — {{ $certificate->certificate_number }}</title>
<style>
    @page { margin: 0; }
    body { margin: 0; padding: 0; font-family: 'DejaVu Sans', Georgia, serif; background: white; }
    .certificate {
        width: 100%; height: 100%;
        background: linear-gradient(135deg, #f0f0ff 0%, #e8e0ff 50%, #f0f0ff 100%);
        position: relative; padding: 60px 80px;
        box-sizing: border-box;
    }
    .border-frame {
        border: 3px solid #4f46e5; padding: 40px 50px;
        height: calc(100% - 120px); box-sizing: border-box;
        position: relative;
    }
    .corner { position: absolute; width: 30px; height: 30px; border-color: #7c3aed; }
    .corner-tl { top: -3px; left: -3px; border-top: 5px solid; border-left: 5px solid; }
    .corner-tr { top: -3px; right: -3px; border-top: 5px solid; border-right: 5px solid; }
    .corner-bl { bottom: -3px; left: -3px; border-bottom: 5px solid; border-left: 5px solid; }
    .corner-br { bottom: -3px; right: -3px; border-bottom: 5px solid; border-right: 5px solid; }
    .content { text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center; }
    .icon { font-size: 50px; color: #4f46e5; margin-bottom: 10px; }
    .title { font-size: 36px; color: #4f46e5; font-weight: bold; letter-spacing: 6px; text-transform: uppercase; margin-bottom: 5px; }
    .subtitle { font-size: 14px; color: #666; letter-spacing: 3px; text-transform: uppercase; margin-bottom: 30px; }
    .presented { font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 2px; }
    .name { font-size: 28px; color: #1a1a3e; font-weight: bold; margin: 10px 0; border-bottom: 2px solid #4f46e5; display: inline-block; padding-bottom: 5px; }
    .desc { font-size: 13px; color: #555; line-height: 1.8; margin: 15px auto; max-width: 80%; }
    .training-name { font-size: 16px; color: #4f46e5; font-weight: bold; margin: 10px 0; }
    .date { font-size: 11px; color: #888; margin-top: 15px; }
    .cert-number { font-size: 10px; color: #aaa; position: absolute; bottom: 15px; right: 20px; }
    .footer-line { width: 150px; border-top: 1px solid #333; margin: 25px auto 5px; }
    .signature-text { font-size: 11px; color: #666; }
</style>
</head><body>
<div class="certificate">
    <div class="border-frame">
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="content">
            <div class="title">Certificate</div>
            <div class="subtitle">of Completion</div>

            <div class="presented">This is proudly presented to</div>
            <div class="name">{{ $certificate->user->name }}</div>

            <div class="desc">
                For successfully completing the training program
            </div>

            <div class="training-name">"{{ $certificate->training->title }}"</div>

            <div class="desc">
                Held at {{ $certificate->training->location }}<br>
                {{ $certificate->training->start_date->format('d F Y') }} — {{ $certificate->training->end_date->format('d F Y') }}
            </div>

            <div class="date">Issued on {{ $certificate->created_at->format('d F Y') }}</div>

            <div class="footer-line"></div>
            <div class="signature-text">Training Management System<br>University Administration</div>
        </div>

        <div class="cert-number">{{ $certificate->certificate_number }}</div>
    </div>
</div>
</body></html>
