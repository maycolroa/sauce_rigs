
<table>
    <thead>
        <tr>
            @foreach($data['data1']['headers'] as $key => $header)
                <th colspan="3" rowspan="3">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr></tr>
        <tr></tr>
        @foreach($data['data1']['data'] as $key => $row)
            <tr>
                @foreach($row as $key2 => $col)
                    <td colspan="3">{{ $col['label'] }} [{{ $col['count'] }}]</td>
                @endforeach
            </tr>
        @endforeach

        @if (COUNT($data['data2']['data']) > 0)

            <tr></tr>
            <tr>
                <td colspan="15">{{ $data['data2']['title'] }}</td>
            </tr>
            <tr></tr>
            <tr>
                <td colspan="8">Peligro</td>
                <td colspan="5">Descripción</td>
                <td colspan="2">Matriz</td>
            </tr>
            @foreach($data['data2']['data'] as $row)
                <tr>
                    <td rowspan="3" colspan="8">{{ $row->name }}</td>
                    <td rowspan="3" colspan="5">{{ $row->danger_description }}</td>
                    <td rowspan="3" colspan="2">{{ $row->matrix }}</td>
                </tr>
                <tr></tr>
                <tr></tr>
            @endforeach

        @endif
    </tbody>
</table>
