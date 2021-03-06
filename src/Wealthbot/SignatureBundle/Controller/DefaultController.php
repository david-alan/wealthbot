<?php
/**
 * Created by JetBrains PhpStorm.
 * User: amalyuhin
 * Date: 14.08.13
 * Time: 16:14
 * To change this template use File | Settings | File Templates.
 */

namespace Wealthbot\SignatureBundle\Controller;


use Wealthbot\SignatureBundle\Adapter\AccountOwnerRecipientAdapter;
use Wealthbot\SignatureBundle\Entity\AccountOwnerSignature;
use Wealthbot\SignatureBundle\Entity\DocumentOwnerSignature;
use Wealthbot\SignatureBundle\Entity\DocumentSignature;
use Wealthbot\SignatureBundle\Model\Envelope;
use Wealthbot\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function checkApplicationSignedAction($account_id)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $accountSignatureManager = $this->get('wealthbot_docusign.document_signature.manager');

        $repository = $em->getRepository('WealthbotClientBundle:ClientAccount');
        $client = $this->getUser();

        $account = $repository->findOneBy(array('id' => $account_id, 'client_id' => $client->getId()));
        if (!$account) {
            return $this->getJsonResponse(array(
                'status' => 'error',
                'message' => 'Account does not exist or does not belong to you.'
            ));
        }

        if (!$accountSignatureManager->isApplicationSigned($account->getId())) {
            $result = array(
                'status' => 'error',
                'message' => 'You have not signed applications. Please sign all applications.'
            );
        } else {
            $result = array('status' => 'success');
        }

        return $this->getJsonResponse($result);
    }

    public function fillPdfAction(Request $request)
    {
        $dir = 'uploads/signature_pdfs';
        $filename = 'IRA_Account_Application';

        $firmName = 'Wealthbot';
        $primaryContact = 'RiaFirst RiaLast';
        $accountType = 'rollover_ira';


        $fdf = '%FDF-1.2
1 0 obj<</FDF<< /Fields[
<</T(firm_name)/V('.$firmName.')>>
<</T(primary_contact)/V('.$primaryContact.')>>
<</T(account_type)/V('.$accountType.')>>
] >> >>
endobj
trailer
<</Root 1 0 R>>
%%EOF';

        file_put_contents($dir . '/' . $filename . '_tmp.fdf', $fdf);

        $command = 'cd uploads/signature_pdfs ';
        $command .= '&& pdftk ' . $filename . '.pdf fill_form ' . $filename . '_tmp.fdf output ' . $filename . '_filled.pdf';

        exec($command);
        unlink($dir . '/' . $filename . '_tmp.fdf');

        die('Complete.');
    }

    private function getJsonResponse(array $data, $code = 200)
    {
        $response = json_encode($data);

        return new Response($response, $code, array('Content-Type'=>'application/json'));
    }
}