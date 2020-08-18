
<table>
    <thead>
        <tr>
            @if ($showLabelCol)
                <th colspan="3" rowspan="3"> </th>
            @endif
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
                @if ($showLabelCol)
                    <td colspan="3">{{ $row[0]['col'] }}</td>
                @endif
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
                <td colspan="7">Peligro</td>
                <td colspan="4">Descripción</td>

                @if ($data['data2']['confLocation']['regional'] == 'SI')
                    <td colspan="1">{{ $keyword['regional'] }}</td>
                @endif
                
                @if ($data['data2']['confLocation']['headquarter'] == 'SI')
                    <td colspan="1">{{ $keyword['headquarter'] }}</td>
                @endif

                @if ($data['data2']['confLocation']['process'] == 'SI')
                    <td colspan="1">{{ $keyword['process'] }}</td>
                @endif

                @if ($data['data2']['confLocation']['area'] == 'SI')
                    <td colspan="1">{{ $keyword['area'] }}</td>
                @endif

            </tr>
            @foreach($data['data2']['data'] as $row)
                <tr>
                    <td rowspan="3" colspan="7">{{ $row->name }}</td>
                    <td rowspan="3" colspan="4">{{ $row->danger_description }}</td>

                    @if ($data['data2']['confLocation']['regional'] == 'SI')
                        <td rowspan="3" colspan="1">{{ $row->regional }}</td>
                    @endif
                    
                    @if ($data['data2']['confLocation']['headquarter'] == 'SI')
                        <td rowspan="3" colspan="1">{{ $row->headquarter }}</td>
                    @endif

                    @if ($data['data2']['confLocation']['process'] == 'SI')
                        <td rowspan="3" colspan="1">{{ $row->process }}</td>
                    @endif

                    @if ($data['data2']['confLocation']['area'] == 'SI')
                        <td rowspan="3" colspan="1">{{ $row->area }}</td>
                    @endif
                </tr>
                <tr></tr>
                <tr></tr>
            @endforeach

        @endif
    </tbody>
</table>
