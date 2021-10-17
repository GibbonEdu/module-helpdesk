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

    $data = [
        'name' => $_POST['name'] ?? '',
        'body' => $_POST['body'] ?? '',
    ];

    if (empty($data['name']) || empty($data['body']) || !$replyTemplateGateway->unique($data, ['name'])) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit();
    } else {
        $helpDeskReplyTemplateID = $replyTemplateGateway->insert($data);

        if ($helpDeskReplyTemplateID === false) {
            $URL .= '&return=error2';
        } else {
            $URL .= '&return=success0&helpDeskReplyTemplateID=' . $helpDeskReplyTemplateID;
        }

        header("Location: {$URL}");
        exit();
    }
}   
?>
