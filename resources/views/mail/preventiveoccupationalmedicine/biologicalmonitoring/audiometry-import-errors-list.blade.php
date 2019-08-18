
<table>
    <thead>
        <tr>
            <th>Fila</th>
            <th colspan="10">Errores</th>
        </tr>
    </thead>
    <tbody>
    @foreach($errors as $key => $item)    
        @foreach($item as $key2 => $error)
        <tr>
            <td >{{ $key }}</td>
            <td colspan="10">
                {{ $error }}
            </td>
        </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
