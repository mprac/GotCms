<?php
/**
 * This source file is part of GotCms.
 *
 * GotCms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * GotCms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along
 * with GotCms. If not, see <http://www.gnu.org/licenses/lgpl-3.0.html>.
 *
 * PHP Version >=5.3
 *
 * @category Modules
 * @package  Blog\Model
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link     http://www.got-cms.com
 */

namespace Modules\Blog\Model;

use Gc\Db\AbstractTable,
    Gc\Document\Model as DocumentModel,
    Zend\Db\Sql\Select;
/**
 * Blog comment table
 */
class Comment extends AbstractTable
{
    /**
     * Table name
     * @var string
     */
    protected $_name ='blog_comment';

    /**
     * Return all documents with comment(s)
     * @return array
     */
    public function getDocumentList()
    {
        $all_comments = $this->getList();
        $documents = array();
        foreach($all_comments as $key => $comment)
        {
            if(empty($documents[$comment['document_id']]))
            {
                $document = DocumentModel::fromId($comment['document_id']);
                $documents[$document->getId()] = $document;
            }
        }

        return $documents;
    }

    /**
     * Return all comments in document
     * @param integer $document_id
     * @return mixte
     */
    public function getList($document_id = NULL)
    {
        return $this->select(function(Select $select) use ($document_id)
        {
            if(!empty($document_id))
            {
                $select->where->equalTo('document_id', $document_id);
            }
        });
    }

    /**
     * Add command
     * @param array $data
     * @param integer $document_id
     * @return boolean
     */
    public function add(array $data, $document_id)
    {
        $available_key = array('message', 'username', 'email');
        $insert_data = array();
        foreach($available_key as $key)
        {
            if(empty($data[$key]))
            {
                return FALSE;
            }
            else
            {
                $insert_data[$key] = $data[$key];
            }
        }

        $insert_data['show_email'] = empty($data['show_email']) ? 0 : 1;
        $insert_data['document_id'] = $document_id;
        $this->insert($insert_data);

        return TRUE;
    }
}