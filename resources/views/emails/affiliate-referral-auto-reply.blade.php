<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
</head>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
  <div style="max-width:560px;margin:0 auto;padding:32px 20px;">
    <div style="background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e5e5;">
      <div style="background:#0A0A0F;padding:28px;text-align:center;">
        <span style="color:#FF7900;font-size:20px;font-weight:800;">Softorio</span>
      </div>
      <div style="padding:32px 28px;">
        <h1 style="font-size:20px;color:#111;margin:0 0 16px;">Thanks for joining, {{ $referral->referrer_name }}!</h1>
        <p style="font-size:14px;color:#444;line-height:1.7;margin:0 0 16px;">
          We've received your affiliate program signup. Remember — you'll earn <strong>20% of the profit</strong> from
          any client you refer, for as long as they stay with Softorio.
        </p>
        <p style="font-size:14px;color:#444;line-height:1.7;margin:0 0 16px;">
          We'll respond within 24 hours with everything you need to get started.
        </p>
        <div style="background:#f9f9f9;border-radius:8px;padding:16px 20px;margin:20px 0;">
          <p style="color:#888;font-size:13px;margin:0 0 8px;">Your message</p>
          <p style="color:#333;font-size:14px;line-height:1.6;white-space:pre-wrap;margin:0;">{{ $referral->message }}</p>
        </div>
        <p style="font-size:14px;color:#444;line-height:1.7;margin:24px 0 0;">
          — The Softorio Team
        </p>
      </div>
    </div>
    <p style="text-align:center;color:#aaa;font-size:12px;margin-top:20px;">Softorio · support@softorio.com</p>
  </div>
</body>
</html>
