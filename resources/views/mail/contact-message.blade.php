<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; color: #1a1a1a;">
    <h2 style="margin-bottom: 4px;">Nowa wiadomość ze strony parafii</h2>
    <p style="margin: 0 0 16px; color: #666;">Formularz kontaktowy</p>

    <table cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td style="font-weight: bold; vertical-align: top;">Imię i nazwisko:</td>
            <td>{{ $name }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top;">E-mail:</td>
            <td>{{ $email }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; vertical-align: top;">Temat:</td>
            <td>{{ $messageSubject }}</td>
        </tr>
    </table>

    <p style="font-weight: bold; margin-top: 20px; margin-bottom: 4px;">Wiadomość:</p>
    <p style="white-space: pre-line;">{{ $message }}</p>
</body>
</html>
