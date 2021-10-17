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

    $replyTemplateGateway = $container->get(ReplyTemplateGateway::class);

    $helpDeskReplyTemplateID = $_POST['helpDeskReplyTemplateID'] ?? '';    

    if (empty($helpDeskReplyTemplateID) || !$replyTemplateGateway->exists($helpDeskReplyTemplateID)) {
        $URL .= '/helpDesk_manageReplyTemplates.php&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        $URL .= '/helpDesk_editReplyTemplate.php';
        $data = [
            'name' => $_POST['name'] ?? '',
            'body' => $_POST['body'] ?? '',
        ];
        if (empty($data['name']) || empty($data['body']) || !$replyTemplateGateway->unique($data, ['name'], $helpDeskReplyTemplateID)) {
            $URL .= '&return=error1';
            header("Location: {$URL}");
            exit();
        } else {
            if (!$replyTemplateGateway->update($helpDeskReplyTemplateID, $data)) {
                $URL .= '&return=error2';
            } else {
                $URL .= '&return=success0&helpDeskReplyTemplateID=' . $helpDeskReplyTemplateID;
            }

            header("Location: {$URL}");
            exit();
        }
    }
}   
?>
