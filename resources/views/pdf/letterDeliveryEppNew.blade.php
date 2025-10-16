<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page { margin: 40px 5px; }
        #header { position: fixed; left: 50px; right: 80px; top: 5px; height: 50px;text-align: center; }
        #header .page:after { content: counter(page); }

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
        #tabla-sin-cebrado tr:nth-child(even) {
            background-color: #ffffff; /* Blanco */
        }
         #tabla-sin-cebrado td, th {
          border: 1px solid #ffffffff;
          text-align: left;
          font-size: 8px;  
          padding: 0px;
        }
    </style>
</head>
<body style="margin: 50px; margin-top: 200px;">
    <!-- Define header and footer blocks before your content -->
     <div id="header">
        <table id="tabla-sin-cebrado">
            <thead>
                <tr>
                    <th colspan="3">                        
                        @if ($delivery->logo)
                        <div style="text-align: left"><img src="{{ public_path('storage/administrative/logos/').$delivery->logo }}" width="150px" height="80px"/></div>
                        @endif
                    </th>
                </tr>
                <tr>
                    <th>Tipo de Documento:</th>
                    <th>Titulo:</th>
                    <th>Version:</th>
                </tr>
                <tr>
                    <td>Formato</td>
                    <td>Formato de entrega de elementos, dotación o equipos</td>
                    <td>4</td>
                </tr>
                <tr>
                    <th>Aprobado por: Nombre</th>
                    <th>Nombre del Archivo:</th>
                    <th>Clase de informacion:</th>
                </tr>
                <tr>
                    <td>{{ $delivery->user_name }}</td>
                    <td>Entrega de elementos, dotación o equipos</td>
                    <td>Interna</td>
                </tr>                
                <tr>
                    <th>Entregado por: Nombre </th>
                    <th>Fecha de Creación:</th>
                    <th>Pagina:</th>
                </tr>
                <tr>
                    <td>{{ $delivery->user_name }}</td>
                    <td>{{$delivery->created_at}}</td>
                    <td><p class="page"></p></td>
                </tr>
            </thead>
        </table>
    </div>
    <br><br>
    <div style="page-break-inside: avoid;">
        <center><b>ENTREGA DE ELEMENTOS DE PROTECCIÓN PERSONAL</b></center>
        <br/><br/>
        <p><b>Nombre empleado: {{ $delivery->employee_name }}</b></p> <p><b>CC: {{$delivery->employee_identification}}</b></p>
        
        <p> {!! nl2br($delivery->text_company) !!} </p>
    </div>
    <div style="page-break-inside: avoid;">
        <table class="table-general">
            <thead> 
                <tr>
                    <td><b>Descripcion del Elemento</b></td>
                    <td><b>Cantidad</b></td>
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
    <div style="page-break-inside: avoid;">
        <table class="table-general">
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
                    <td colspan='2'><b>Observaciones</b></td>
                </tr>          
                <tr>
                    <td colspan='2'>{{ $delivery->observations }}</td>
                </tr>
            </thead>
        </table>        
    </div>
    <br><br><br><br>
    <div style="page-break-inside: avoid;">
        <table id="tabla-sin-cebrado">
            <tr>
                <td style="text-align: center; padding: 0px">
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