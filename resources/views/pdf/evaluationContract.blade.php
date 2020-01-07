<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body: {
            padding-bottom: 100px;
            padding-right: 100px;
            padding-left : 100px;
            padding-top: 0px;
        }
    </style>
</head>
<body style="margin: 50px; margin-top: 0px;">
    <center><b>Evaluación</b></center>
    <br/><br/>

    <table>
        <thead>
            <tr>
                <th scope="col"><b>Nit:</b></th>
                <th scope="col">{{$evaluations->contract->nit}}</th>

                <th scope="col"><b>Nombre del coordinador de gestión ambiental:</b></th>
                <th scope="col">{{$evaluations->contract->legal_representative_name}}</th>
            </tr>
            <tr>
                <th scope="col"><b>Dirección:</b></th>
                <th scope="col">{{$evaluations->contract->address}}</th>

                <th scope="col"><b>Nombre del coordinador de SST:</b></th>
                <th scope="col">{{$evaluations->contract->SG_SST_name}}</th>
            </tr>
            <tr>
                <th scope="col"><b>Teléfono:</b></th>
                <th scope="col">{{$evaluations->contract->phone}}</th>

                <th scope="col"><b>Actividad económica general de la empresa:</b></th>
                <th scope="col">{{$evaluations->contract->economic_activity_of_company}}</th>
            </tr>
            <tr>
                <th scope="col"><b>N° de personas que laboran en el HPTU:</b></th>
                <th scope="col">{{$evaluations->contract->number_workers}}</th>

                <th scope="col"><b>Clase de riesgo:</b></th>
                <th scope="col">{{$evaluations->contract->high_risk_work}}</th>
            </tr>
        </thead>
    </table>
</body>
</html>