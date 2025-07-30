<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Users\LogUserModify;

class AsignedPasswordCoesco extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asigned-password-coesco';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignacion de contraseñas a usuarios de Coesco';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userPassword = [
            ['EDSMartinica@Coescocolombia.com',	 "EDSMartinica2025*"],
            ['edsjardin@coescocolombia.com',	 'EDSJardin2025*'],
            ['edsmatamundo@coescocolombia.com',	 'EDSMatamundo2025*'],
            ['edslapista@coescocolombia.com',	 'EDSLapista2025*'],
            ['edslavictoria@coescocolombia.com',	 'EDSLavictoria2025*'],
            ['edsipanema@coescocolombia.com',	 'EDSIpanema2025^*'],
            ['edscanaima@coescocolombia.com',	 'EDSCanaima2025*'],
            ['edspasodelabarca@coescocolombia.com',	 'EDSPasodelabarca2025*'],
            ['edslualcor@coescocolombia.com',	 'EDSLualcor2025*'],
            ['edstabor1@coescocolombia.com',	 'EDSTabor12025*'],
            ['edstabor2@coescocolombia.com',	 'EDSTabor22025*'],
            ['edsaures@coescocolombia.com',	 'EDSAures2025*'],
            ['edsavenidabolivar@coescocolombia.com',	 'EDSAvbolivar2025*'],
            ['edsbrisasdellago@coescocolombia.com',	 'EDSBrisasdellago2025*'],
            ['edsalhambra@coescocolombia.com',	 'EDSAlhambra2025*'],
            ['edseldiamante@coescocolombia.com',	 'EDSEldiamante2025*'],
            ['edssanmartin@coescocolombia.com',	 'EDSSanmartin2025*'],
            ['edselretorno@coescocolombia.com',	 'EDSElretorno2025*'],
            ['edscampohermoso@coescocolombia.com',	 'EDSCampohermoso2025*'],
            ['edssanesteban@coescocolombia.com',	 'EDSSanesteban2025*'],
            ['edscalarca@coescocolombia.com',	 'EDSCalarca2025*'],
            ['edslafloresta@coescocolombia.com',	 'EDSLafloresta2025*'],
            ['edseluval@coescocolombia.com',	 'EDSEluval2025*'],
            ['edsellido@coescocolombia.com',	 'EDSEllido2025*'],
            ['edsguayabal@coescocolombia.com',	 'EDSGuayabal2025*'],
            ['edsrobledo@coescocolombia.com',	 'EDSRobledo2025*'],
            ['edssanrafael@coescocolombia.com',	 'EDSSanrafael2025*'],
            ['edscaribe@coescocolombia.com',	 'EDSCaribe2025*'],
            ['edslamilagrosa@coescocolombia.com',	 'EDSLamilagrosa2025*'],
            ['edsmarcella@coescocolombia.com',	 'EDSMarcella2025*'],
            ['edsgiron@coescocolombia.com',	 'EDSGiron2025*'],
            ['edsbelenmedellin@coescocolombia.com',	 'EDSBelenmedellin2025*'],
            ['edslapradera@coescocolombia.com',	 'EDSLapradera2025*'],
            ['edsestrelladelsur@coescocolombia.com',	 'EDSEstrelladelsur2025*'],
            ['edsgalaxia@coescocolombia.com',	 'EDSGalaxia2025*'],
            ['edslaisabella@coescocolombia.com',	 'EDSLaisabella2025*'],
            ['edslanorte@coescocolombia.com',	 'EDSLanorte2025*'],
            ['edsavenidadelrio@coescocolombia.com',	 'EDSAvdelrio2025*'],
            ['edspoblado@coescocolombia.com',	 'EDSPoblado2025*'],
            ['edsaltoprado@coescocolombia.com',	 'EDSAltoprado2025*'],
            ['edsprado@coescocolombia.com',	 'EDSPrado2025*'],
            ['edslaonce@coescocolombia.com',	 'EDSLaonce2025*'],
            ['edsboston@coescocolombia.com',	 'EDSBoston2025*'],
            ['edselbosque@coescocolombia.com',	 'EDSElbosque2025*'],
            ['edssanbenito@coescocolombia.com',	 'EDSSanbenito2025*'],
            ['edsmalambo@coescocolombia.com',	 'EDSMalambo2025*'],
            ['edsjuanmina@coescocolombia.com',	 'EDSJuanmina2025*'],
            ['edspuertadorada@coescocolombia.com',	 'EDSPuertadorada2025*'],
            ['edscentroecopetrol@coescocolombia.com',	 'EDSCentroecopetrol2025*'],
            ['edssabanalarga@coescocolombia.com',	 'EDSSabanalarga2025*'],
            ['edspuentepumarejo@coescocolombia.com',	 'EDSPuentepumarejo2025*'],
            ['edselamparo@coescocolombia.com',	 'EDSElamparo2025*'],
            ['edsretornobureche@coescocolombia.com',	 'EDSBureche2025*'],
            ['edschusaca@coescocolombia.com',	 'EDSChusaca2025*'],
            ['edsespiritusanto@coescocolombia.com',	 'EDSEspiritusanto2025*'],
            ['edsemanuel@coescocolombia.com',	 'EDSEmanuel2025*'],
            ['edsterminal@coescocolombia.com',	 'EDSTerminal2025*'],
            ['edsamericas@coescocolombia.com',	 'EDSAmericas2025*'],
            ['edscundinamarca@coescocolombia.com',	 'EDSCundinamarca2025*'],
            ['edssanjose@coescocolombia.com',	 'EDSSanjose2025*'],
            ['edsunionroma@coescocolombia.com',	 'EDSUnionroma2025*'],
            ['edsdisernecom@coescocolombia.com',	 'EDSDisernecom2025*'],
            ['edslimonar@coescocolombia.com',	 'EDSLimonar2025*'],
            ['edsguatiquia@coescocolombia.com',	 'EDSGuatiquia2025*'],
            ['edsandalucia@coescocolombia.com',	 'EDSAndalucia2025*'],
            ['edsbelen@coescocolombia.com',	 'EDSBelen2025*'],
            ['edssantarosa@coescocolombia.com',	 'EDSSantarosa2025*'],
            ['edssanmiguel@coescocolombia.com',	 'EDSSanmiguel2025*'],
            ['edsalcazar@coescocolombia.com',	 'EDSAlcazar2025*'],
            ['edsparalela@coescocolombia.com',	 'EDSParalela2025*'],
            ['edslosangeles@coescocolombia.com',	 'EDSLosangeles2025*'],
            ['edsalcaravan@coescocolombia.com',	 'EDSAlcaravan2025*'],
            ['edsjerusalem@coescocolombia.com',	 'EDSJerusalem2025*'],
            ['edscordoba@coescocolombia.com',	 'EDSCordoba2025*'],
            ['edssancristobal@coescolombia.com',	 'EDSSancristobal2025*'],
            ['edsshalom@coescocolombia.com',	 'EDSShalom2025*'],
            ['edslaesperanza@coescocolombia.com',	 'EDSLaesperanza2025*'],
            ['edsjardindegachancipa@coescocolombia.com',	 'EDSGachancipa2025*'],
            ['tiendachusaca@coescocolombia.com',	 'EDSTiendachusaca2025*'],
            ['tiendaguatiquia@coescocolombia.com',	 'EDSTiendaguatiquia2025*'],
            ['tiendalosangeles@coescocolombia.com',	 'EDSTiendalosangeles2025*']
        ];

        foreach ($userPassword as $user) 
        {
            $userModel = User::where('email', $user[0])->first();
            if ($userModel) {
                $userModel->password = $user[1];
                $userModel->save();

                $log_modify = new LogUserModify;
                $log_modify->company_id = 733;
                $log_modify->modifier_user = 5;
                $log_modify->modified_user = $userModel->id;
                $log_modify->modification = "Contraseña actualizada para el usuario";
                $log_modify->save();
            } else {
                \Log::info("Usuario no encontrado: {$user[0]}");
            }
        }
    }
}
