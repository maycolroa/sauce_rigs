<table>
    <thead>
        <tr>
            @if ($logo)
            <th rowspan="3" colspan="2" style='border: 1px solid black; padding: 1px; width: 20%'><img src="{{ public_path('storage/administrative/logos/').$logo }}" width="50px" height="50px"/></th>
            @endif
            <th rowspan="3" colspan="10" style='padding: 1px; width: 20%'>
                <p>Nombre: {{$matrix_name}}</p>
                <p>Fuente: {{$source}} </p>
                <p>Observaciones: {{$observations}}</p>
            </th>
        </tr>
        <tr></tr>
        <tr></tr>
    </thead>
</table>
<table>
    <thead>
        <tr>
            @foreach($headings as $key => $header)
                <th colspan="1" rowspan="1">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $row)
            <tr>
                @foreach($row as $key2 => $col)
                    <td colspan="1">{{ $col }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
