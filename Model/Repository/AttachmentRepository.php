<?php

namespace ScoutUnitsList\Model\Repository;

use ScoutUnitsList\Manager\DbManager;
use ScoutUnitsList\Model\Attachment;

/**
 * Attachment repository
 */
class AttachmentRepository extends NativeRepository
{
    /** @const string */
    const NAME = 'posts';

    /**
     * Get model
     *
     * @return string
     */
    protected function getModel()
    {
        return Attachment::class;
    }

    /**
     * Define structure
     */
    protected function defineStructure()
    {
        $this
            ->setStructureElement('id', DbManager::TYPE_DECIMAL, 'ID', true)
            ->setStructureElement('authorId', DbManager::TYPE_DECIMAL, 'post_author')
            ->setStructureElement('title', DbManager::TYPE_STRING, 'post_title')
            ->setStructureElement('slug', DbManager::TYPE_STRING, 'post_name')
            ->setStructureElement('url', DbManager::TYPE_STRING, 'guid')
            ->setStructureElement('type', DbManager::TYPE_STRING, 'post_type')
            ->setStructureElement('mimeType', DbManager::TYPE_STRING, 'post_mime_type')
            ->setStructureElement('createdAt', DbManager::TYPE_DATETIME, 'post_date')
            ->setStructureElement('updatedAt', DbManager::TYPE_DATETIME, 'post_modified')
        ;
    }

    /**
     * Get by
     *
     * @param array    $conditions conditions
     * @param array    $order      order
     * @param int|null $limit      limit
     * @param int      $offset     offset
     *
     * @return array
     */
    public function getBy(array $conditions, array $order = [], $limit = null, $offset = 0)
    {
        $conditions['type'] = Attachment::TYPE;
        $attachments = parent::getBy($conditions, $order, $limit, $offset);

        return $attachments;
    }

    /**
     * Find matched titles
     * 
     * @param string $title       title
     * @param array  $categoryIds category IDs
     * @param int    $limit       limit
     *
     * @return array
     */
    public function findMatchedTitles($title, array $categoryIds, $limit = 10)
    {
        $query = $this->db->prepare('
            SELECT DISTINCT `ID`, `post_title` FROM `' . $this->getTableName() . '` p
                INNER JOIN `' . $this->getTableName('term_relationships') . '` tr ON p.`id` = tr.`object_id`
                INNER JOIN `' . $this->getTableName('term_taxonomy') . '` tt ON tr.`term_taxonomy_id` = tt.`term_taxonomy_id`
            WHERE tt.`term_id` IN (:categoryIds) && `post_type` = :postType && `post_title` LIKE :title LIMIT ' .
            ((int) $limit))
            ->setParam('categoryIds', $categoryIds)
            ->setParam('postType', Attachment::TYPE)
            ->setParam('title', '%' . $this->escapeLike($title) . '%')
            ->getQuery();
        $results = $this->db->getResults($query, ARRAY_A);

        $list = [];
        foreach ($results as $result) {
            $list[$result['ID']] = $result['post_title'];
        }

        return $list;
    }
}
