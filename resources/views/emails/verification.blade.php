<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<div>
    Thanks for creating an account of dribbler.
    Please follow the link below to verify your email address
    {{ URL::to('/register/verify/') . $confirmation_code . "?email=" . $email }}
</div>

</body>
</html>