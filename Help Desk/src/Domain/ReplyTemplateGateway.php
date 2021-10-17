<?php
namespace Gibbon\Module\HelpDesk\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

/**
 * Reply Template Gateway
 */
class ReplyTemplateGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'helpDeskReplyTemplate';
    private static $primaryKey = 'helpDeskReplyTemplateID';
    private static $searchableColumns = [];

    public function queryTemplates($critera) {
        $query = $this
            ->newQuery()
            ->from($this->getTableName())
            ->cols([
                'helpDeskReplyTemplateID', 'name', 'body'
            ]);

        return $this->runQuery($query, $critera);
    }

}
