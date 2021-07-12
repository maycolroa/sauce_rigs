
<table>
    <thead>        
        <tr>
            <td colspan="8">{{ $data['title'] }}</td>
        </tr>
        <tr>
            @if ($data['confLocation']['process'] == 'SI')
                <td colspan="2">{{ $keyword['process'] }}</td>
            @endif

            @if ($data['confLocation']['area'] == 'SI')
                <td colspan="2">{{ $keyword['area'] }}</td>
            @endif

            <td colspan="1">Riesgo</td>
            <td colspan="3">Evento de Riesgo</td>
        </tr>
    </thead>
    <tbody>
        @foreach($data['data'] as $row)
            <tr>
                @if ($data['confLocation']['process'] == 'SI')
                    <td colspan="2">{{ $row['process'] }}</td>
                @endif

                @if ($data['confLocation']['area'] == 'SI')
                    <td colspan="2">{{ $row['area'] }}</td>
                @endif

                <td colspan="1">R.{{ $row['sequence'] }}</td>
                <td colspan="3">{{ $row['risk_name'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
