<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="date=no">
    <meta name="format-detection" content="address=no">
    <meta name="format-detection" content="email=no">
    <meta name="author" content="HansForms">
    <title>HansForms</title>
</head>
<body>
<div>
    <h1>HansForms</h1>
    <hr>
    <h3>You have been invited to complete the form!</h3>
    <p>Hello! We would like to inform you that you have just been invited to answer the form called &quot;{{$formName}}&quot;. This
        form is opened from {{$startTime}} to {{$endTime}}. Please, don't forget to complete it on time -
        after this date, it won't be possible.</p>
    <p>To access it, click <a style="font-weight: bold" href="{{$privateLink}}">here</a>.</p>
    <hr>
    <p>Have a nice day!<br>HansForms
    </p>
</div>
</body>
</html>
