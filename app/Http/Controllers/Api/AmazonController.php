<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\General\EmailBlackList;
use Response;
use Log;

class AmazonController extends Controller
{
    CONST COMPLAINT_DETAIL = [
        'abuse' => 'Indica correo electrónico no solicitado o algún otro tipo de abuso de correo electrónico.',
        'auth-failure' => 'Informe de error de autenticación de correo electrónico.',
        'fraud' => 'Indica algún tipo de fraude o actividad de phishing.',
        'not-spam' => 'Indica que la entidad que proporciona el informe no considera que el mensaje sea spam. Esto puede usarse para corregir un mensaje que fue etiquetado incorrectamente o categorizado como spam.',
        'other' => 'Indica cualquier otro comentario que no se ajuste a otros tipos registrados.',
        'virus' => 'Informa que se encuentra un virus en el mensaje de origen.'
    ];

    public function handleBounceOrComplaint(Request $request)
    {
        Log::info($request->json()->all());
        $data = $request->json()->all();
        
        if ($request->json('Type') == 'SubscriptionConfirmation')
            Log::info("SubscriptionConfirmation came at: ".$data['Timestamp']);
        
        if ($request->json('Type') == 'Notification')
        {
            $message = json_decode($request->json('Message'), true);

            switch ($message['notificationType'])
            {
                case 'Bounce':
                    $bounce = $message['bounce'];

                    if ($bounce['bounceType'] == 'Permanent')
                    {
                        foreach ($bounce['bouncedRecipients'] as $bouncedRecipient)
                        {
                            $emailRecord = EmailBlackList::updateOrCreate(
                                ['email' => $bouncedRecipient['emailAddress'], 'problem_type' => 'bounce'],
                                [
                                    'email' => $bouncedRecipient['emailAddress'],
                                    'problem_type' => 'bounce',
                                    'detail' => isset($bouncedRecipient['action']) ? $bouncedRecipient['action'] : NULL,
                                    'diagnostic' => isset($bouncedRecipient['diagnosticCode']) ? $bouncedRecipient['diagnosticCode'] : NULL
                                ]
                            );
                        }
                    }

                break;

                case 'Complaint':

                    $complaint = $message['complaint'];
                    
                    foreach($complaint['complainedRecipients'] as $complainedRecipient)
                    {
                        $emailRecord = EmailBlackList::updateOrCreate(
                            ['email' => $complainedRecipient['emailAddress'], 'problem_type' => 'complaint'],
                            [
                                'email' => $complainedRecipient['emailAddress'],
                                'problem_type' => 'complaint',
                                'detail' => isset($complaint['complaintFeedbackType']) ? $complaint['complaintFeedbackType'] : NULL,
                                'diagnostic' => 
                                    isset($complaint['complaintFeedbackType']) && isset($this::COMPLAINT_DETAIL[$complaint['complaintFeedbackType']]) ? $this::COMPLAINT_DETAIL[$complaint['complaintFeedbackType']] : NULL
                            ]
                        );
                    }

                break;

                default:
                // Do Nothing
                break;
            }
        }

        return Response::json(['status' => 200, "message" => 'success']);
    }
}