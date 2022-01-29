<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
          font-family: arial, sans-serif;     
          font-size: 13px;     
          width: 100%;
        }

        td, th {
          border: 1px solid #000000;
          text-align: center;
          font-size: 13px;  
          padding: 8px;
        }

        tr:nth-child(even) {
          background-color: #dddddd;
        }

        body{
            font-size: 13px;
            font-family: arial, sans-serif;  
            text-align: justify
        }

        .body-themes {
            background-color: white;
        }

        .title-obj {
          text-align: justify;
        }

        .title-sub {
            text-align: justify;
        }
    </style>
</head>
<body style="margin: 50px; margin-top: 0px;">
    <!-- Define header and footer blocks before your content -->
    @if ($delivery->logo)
    <div style="text-align: right"><img src="{{ public_path('storage/administrative/logos/').$delivery->logo }}" width="120px" height="120px"/></div>
    @endif
    <br/><br>
    <center><b>ENTREGA DE ELEMENTOS DE PROTECCIÓN PERSONAL</b></center>
    <br/><br/>
    <p><b>Nombre empleado: {{ $delivery->employee_name }}</b></p> <p><b>CC: {{$delivery->employee_identification}}</b></p>
    
    <p> {!! nl2br($delivery->text_company) !!} </p>
    <br><br>
    <div >
        <table class="table-general">
            <thead> 
                <tr>
                    <th>Descripcion del Elemento</th>
                    <th>Cantidad</th>
                </tr>                   
                @foreach($delivery->elements as $element)
                    <tr>
                        <td >{{ $element["name"] }}</td>
                        <td >{{ $element["quantity"] }}</td>
                    </tr>
                @endforeach
            </thead>
        </table>
    </div>
    <br><br>
    <div>
        <table>
            <thead> 
                <tr>
                    <th>Fecha de entrega</th>
                    <td>{{$delivery->created_at}}</td>
                </tr>     
                <tr>
                    <th>Nombre de quien realizo la entrega</th>
                    <td>{{ $delivery->user_name }}</td>
                </tr>                              
                <tr>
                    <th colspan='2'>Observaciones</th>
                </tr>          
                <tr>
                    <td colspan='2'>{{ $delivery->observations }}</td>
                </tr>
            </thead>
        </table>        
    </div>
    <br><br><br><br>
    <div style="page-break-inside: avoid;">
        <table style="background-color: white;">
            <tr style="background-color: white;">
                <td style="border: 0px solid #dddddd; text-align: center; padding: 0px">
                    @if ($delivery->firm)
                        <center>
                            <img src="{{$delivery->firm}}" width="150px" height="75px"/>
                        </center>
                    @else
                        <center>
                            _____________________________________
                        </center>
                    @endif
                    <p style="text-align: center; font-size: 12px;"><b>{{ $delivery->employee_name }}</b><p>
                    <p style="text-align: center; font-size: 12px;"><b>{{ $delivery->employee_identification }}</b></p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>