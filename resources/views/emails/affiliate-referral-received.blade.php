<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
  <div style="max-width:560px;margin:0 auto;padding:32px 20px;">
    <div style="background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e5e5;">
      <div style="background:#FF7900;padding:20px 28px;">
        <span style="color:#fff;font-size:18px;font-weight:700;">New Affiliate Program Signup</span>
      </div>
      <div style="padding:28px;">
        <table style="width:100%;border-collapse:collapse;font-size:14px;color:#333;">
          <tr><td style="padding:6px 0;color:#888;width:120px;">Name</td><td style="padding:6px 0;font-weight:600;">{{ $referral->referrer_name }}</td></tr>
          <tr><td style="padding:6px 0;color:#888;">Email</td><td style="padding:6px 0;"><a href="mailto:{{ $referral->referrer_email }}" style="color:#FF7900;">{{ $referral->referrer_email }}</a></td></tr>
          @if($referral->referrer_phone)
          <tr><td style="padding:6px 0;color:#888;">Phone</td><td style="padding:6px 0;">{{ $referral->referrer_phone }}</td></tr>
          @endif
        </table>
        <div style="margin-top:20px;padding-top:20px;border-top:1px solid #eee;">
          <p style="color:#888;font-size:13px;margin:0 0 8px;">Message</p>
          <p style="color:#333;font-size:14px;line-height:1.6;white-space:pre-wrap;margin:0;">{{ $referral->message }}</p>
        </div>
      </div>
    </div>
    <p style="text-align:center;color:#aaa;font-size:12px;margin-top:20px;">Softorio affiliate program · {{ now()->format('d M Y, h:i A') }}</p>
  </div>
</body>
</html>
