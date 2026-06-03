<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi Email</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px; color: #333;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 8px; border-top: 4px solid #8b1515; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
        <h2 style="color: #8b1515; margin-top: 0;">Verifikasi Email Anda</h2>
        <p>Halo, <strong>{{ $userName }}</strong>.</p>
        <p>Kami menerima permintaan untuk memverifikasi alamat email ini. Gunakan kode OTP berikut untuk menyelesaikan proses verifikasi:</p>
        
        <div style="background-color: #f4f4f4; border-radius: 6px; padding: 15px; text-align: center; margin: 25px 0;">
            <span style="font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #333;">{{ $otp }}</span>
        </div>
        
        <p style="font-size: 13px; color: #666;">Kode ini berlaku selama <strong>{{ $validMinutes }} menit</strong>. Jangan berikan kode ini kepada siapapun.</p>
        <p style="font-size: 13px; color: #666;">Jika Anda tidak merasa meminta kode ini, Anda dapat mengabaikan email ini.</p>
        <hr style="border: none; border-top: 1px solid #eaeaea; margin: 30px 0;">
        <p style="font-size: 12px; color: #999; text-align: center;">&copy; {{ date('Y') }} Rekrutmen TUS. Hak cipta dilindungi.</p>
    </div>
</body>
</html>
