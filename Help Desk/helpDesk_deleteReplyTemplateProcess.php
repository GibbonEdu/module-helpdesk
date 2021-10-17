<?php

use Gibbon\Module\HelpDesk\Domain\ReplyTemplateGateway;

require_once '../../gibbon.php';

$URL = $session->get('absoluteURL') . '/index.php?q=/modules/' . $session->get('module');

if (!isActionAccessible($guid, $connection2, '/modules/Help Desk/helpDesk_manageReplyTemplates.php')) {
    //Acess denied
    $URL .= '/issues_view.php&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    $URL .= '/helpDesk_manageReplyTemplates.php';

    $replyTemplateGateway = $container->get(ReplyTemplateGateway::class);

    $helpDeskReplyTemplateID = $_POST['helpDeskReplyTemplateID'] ?? '';

    if (empty($helpDeskReplyTemplateID) || !$replyTemplateGateway->exists($helpDeskReplyTemplateID)) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        if ($replyTemplateGateway->delete($helpDeskReplyTemplateID)) {
            $URL .= '&return=success0';
        } else {
            $URL .= '&return=error2';
        }

        header("Location: {$URL}");
        exit();
    }
}   
?>
