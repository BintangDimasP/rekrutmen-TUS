<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode Reset Password</title>
</head>
<body style="margin:0; padding:0; font-family: 'Helvetica Neue', Arial, sans-serif; background:#f3f4f6; color:#1f2937;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6; padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="100%" style="max-width:520px; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.06);" cellpadding="0" cellspacing="0">

                    {{-- Header --}}
                    <tr>
                        <td style="background:#8b1515; padding:28px 32px; color:#fff;">
                            <div style="font-size:12px; font-weight:600; letter-spacing:2px; text-transform:uppercase; opacity:0.85;">Telkom University</div>
                            <div style="font-size:20px; font-weight:700; margin-top:4px;">Reset Password</div>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px 32px 24px;">
                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6; color:#1f2937;">
                                Halo <strong>{{ $userName ?: 'Pengguna' }}</strong>,
                            </p>
                            <p style="margin:0 0 28px; font-size:15px; line-height:1.6; color:#4b5563;">
                                Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode di bawah ini untuk melanjutkan proses reset password.
                            </p>

                            {{-- OTP block — tanpa border/background merah, cukup bold hitam besar --}}
                            <div style="text-align:center; margin-bottom:28px; padding:20px 0;">
                                <div style="font-size:11px; font-weight:600; color:#6b7280; letter-spacing:3px; text-transform:uppercase; margin-bottom:12px;">Kode OTP</div>
                                <div style="font-size:44px; font-weight:800; letter-spacing:12px; color:#111827; font-family:'Courier New', Courier, monospace; line-height:1;">{{ $otp }}</div>
                            </div>

                            <p style="margin:0 0 8px; font-size:14px; line-height:1.6; color:#4b5563;">
                                Kode ini berlaku selama <strong style="color:#111827;">{{ $minutesValid }} menit</strong>. Jangan bagikan kode ini kepada siapapun.
                            </p>
                            <p style="margin:24px 0 0; font-size:13px; line-height:1.6; color:#9ca3af;">
                                Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f9fafb; padding:18px 32px; text-align:center; border-top:1px solid #e5e7eb;">
                            <div style="font-size:12px; color:#9ca3af;">
                                Email otomatis — mohon tidak membalas pesan ini.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
