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
 * @category    Gc
 * @package     Library
 * @subpackage  Datatype
 * @author      Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license     GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link        http://www.got-cms.com
 */

namespace Gc\Datatype;

use Gc\Db\AbstractTable,
    Gc\Document\Model as DocumentModel,
    Gc\Media\Info,
    Gc\Property,
    Gc\Registry,
    ReflectionObject,
    Zend\View\Model\ViewModel,
    Zend\View\Resolver\TemplatePathStack;
/**
 * Abstract Datatype is used to call
 * the prevalue editor and editor.
 *
 * @category    Gc
 * @package     Library
 * @subpackage  Datatype
 */
abstract class AbstractDatatype extends AbstractTable
{
    /**
     * Editor
     * @var \Gc\Datatype\AbstractDatatype\AbstractEditor
     */
    protected     $_editor;

    /**
     * Prevalue editor
     * @var \Gc\Datatype\AbstractDatatype\AbstractPrevalueEditor
     */
    protected     $_prevalueEditor;

    /**
     * Property
     * @var \Gc\Property\Model
     */
    protected     $_property;

    /**
     * Table name
     * @var string
     */
    protected     $_name = 'datatype';

    /**
     * Get Datatype Editor
     * @param Property\Model $property
     * @return \Gc\Datatype\AbstractDatatype\AbstractEditor
     */
    abstract public function getEditor(Property\Model $property);

    /**
     * Get Datatype Prevalue editor
     * @return \Gc\Datatype\AbstractDatatype\AbstractPrevalueEditor
     */
    abstract public function getPrevalueEditor();

    /**
     * Return datatype informations
     * @return array
     */
    public function getInfos()
    {
        $object = new ReflectionObject($this);
        $directory = dirname($object->getFileName());
        $filename = $directory . '/datatype.info';
        $info = new Info();

        if($info->fromFile($filename) !== TRUE)
        {
            return FALSE;
        }

        return $info->render();
    }

    /**
     * Load Datatype
     * @param Model $datatype
     * @param integer $document_id
     * @return mixed
     */
    public function load($datatype = NULL, $document_id = NULL)
    {
        if(empty($datatype))
        {
            return FALSE;
        }

        $this->setData('datatype_model', $datatype);
        $this->setData('document_id', $document_id);
    }

    /**
     * Return configuration
     * @return mixed
     */
    public function getConfig()
    {
        return $this->getDatatypeModel()->getData('prevalue_value');
    }

    /**
     * Set configuration
     * @param mixed $value
     * @return \Gc\Datatype\AbstractDatatype
     */
    public function setConfig($value)
    {
        $this->getDatatypeModel()->setData('prevalue_value', $value);
        return $this;
    }

    /**
     * Get upload url path
     * @param integer $property_id
     * @return string
     */
    public function getUploadUrl($property_id)
    {
        $router = Registry::get('Application')->getMvcEvent()->getRouter();

        return $router->assemble(array('document_id' => $this->getDocumentId(), 'property_id' => $property_id), array('name' => 'mediaUpload'));
    }

    /**
     * Get a helper by name
     *
     * @param  string $name
     * @return object
     */
    public function getHelper($name)
    {
        if($this->getHelperBroker() === NULL)
        {
            $this->setHelperBroker(Registry::get('Application')->getServiceManager()->get('viewhelpermanager'));
        }

        return $this->getHelperBroker()->get($name);
    }

    /**
     * Get Property Model
     *
     * @return \Gc\Property\Model
     */
    public function getProperty()
    {
        return $this->_property;
    }

    /**
     * Set Property Model
     * @param \Gc\Property\Model $property
     * @return \Gc\Datatype\AbstractDatatype
     */
    public function setProperty($property)
    {
        $this->_property = $property;
        return $this;
    }

    /**
     * get datatype name, construct with datatype name and property_id
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Render template
     *
     * @param string $name
     * @param array $data
     * @return string
     */
    public function render($name, array $data = array())
    {
        $renderer = Registry::get('Application')->getServiceManager()->get('Zend\View\Renderer\PhpRenderer');

        $view_model = new ViewModel();
        $view_model->setTemplate($name);
        $view_model->setVariables($data);
        return $renderer->render($view_model);
    }

    /**
     * Add path in Zend\View\Resolver\TemplatePathStack
     *
     * @param string $dir
     * @return \Gc\Datatype\AbstractDatatype\AbstractPrevalueEditor
     */
    public function addPath($dir)
    {
        $renderer = Registry::get('Application')->getServiceManager()->get('Zend\View\Renderer\PhpRenderer');
        $iterators = $renderer->resolver()->getIterator()->toArray();
        foreach($iterators as $iterator)
        {
            if($iterator instanceof TemplatePathStack)
            {
                $iterator->addPath($dir);
            }
        }

        return $this;
    }

    /**
     * Retrieve document
     *
     * @return \Gc\Document\Model
     */
    public function getDocument()
    {
        if($this->getData('document') === NULL)
        {
            $this->setData('document', DocumentModel::fromId($this->getDocumentId()));
        }

        return $this->getData('document');
    }
}
