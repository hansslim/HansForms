<!doctype html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
@if ($forms->count() >= 1)
@foreach($forms as $form)
    type: {{$form->type}}<br>
    question: {{$form->header}}<br>
    id: {{$form->input_element_id}}<br>
    order: {{$form->order}}<br>
    <br><br>
@endforeach
@endif
</body>
</html>
