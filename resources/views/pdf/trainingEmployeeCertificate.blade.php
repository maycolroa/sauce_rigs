<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            text-align: justify;
            font-size: 16px;
            font-family: arial, sans-serif;   
            line-height: 30px;
        }
    </style>
</head>
<body style="margin: 50px; margin-top: 0px;">
    <!-- Define header and footer blocks before your content -->
    <div style="text-align: left"><img src="{{ public_path('images/Sauce-ML Logo RiGS Principal.png') }}" width="220px" height="150px"/></div>
    <br/>
    <br/><br/>
    <center><b>SAUCE - CONTRATISTAS</b></center>
    <br/><br/>
    <p>Notifica que el empleado <b>{{$training->employee->name}}</b>, identificado(a) con el número de documento <b>{{$training->employee->identification}}</b>, perteneciente a la empresa contratistas <b>{{$training->contract->social_reason}}</b>, a probado la capacitación {{$training->name}}, asignada por la empresa contratante <b>{{$training->company->name}}</b>.</p>

    <br><br>
    <p>Fecha de aprobación: <b>{{$training->date_approver}}</b>.
</body>
</html>