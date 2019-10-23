<tr>
    <td class="header">
        <table style="width: 100%; background-color: #f0635f;">
            <tbody>
                <tr>
                    <td style="width: 50%; padding: 10px">
                        <center>
                            <img src="https://sauce.rigs.com.co/images/SAUCE_Logo RiGS barra lateral izquierda.png" style="width: 250px; heigth: 60px">
                        </center>
                    </td>
                    <td style="width: 50%; padding: 10px">
                        <center>
                            <div style="font-family: sans-serif; font-size: 30px; text-decoration: underline; text-underline-position: under; color: #faf5f5; text-transform: uppercase;">
                                @isset($logo) 
                                    <img src="{{ 'https://sauce.rigs.com.co/images/'.$logo }}" style="width: 80px !important;vertical-align: middle;"> 
                                @endisset
                                 {{ $title ?? '' }}
                            </div>
                        </center>
                    </td>
                </tr>
            </tbody>
        </table>
    </td>
</tr>
