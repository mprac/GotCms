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
 * @category Gc_Tests
 * @package  ZfModules
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link     http://www.got-cms.com
 */

namespace Development\Controller;

use Gc\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Gc\Layout\Model as LayoutModel;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2013-03-15 at 23:50:27.
 *
 * @group    ZfModules
 * @category Gc_Tests
 * @package  ZfModules
 */
class LayoutControllerTest extends AbstractHttpControllerTestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->init();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::indexAction
     *
     * @return void
     */
    public function testIndexAction()
    {
        $this->dispatch('/admin/development/layout');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::createAction
     *
     * @return void
     */
    public function testCreateAction()
    {
        $this->dispatch('/admin/development/layout/create');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/create');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::createAction
     *
     * @return void
     */
    public function testCreateActionWithInvalidPostData()
    {
        $this->dispatch(
            '/admin/development/layout/create',
            'POST',
            array(
            )
        );

        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/create');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::createAction
     *
     * @return void
     */
    public function testCreateActionWithPostData()
    {
        $this->dispatch(
            '/admin/development/layout/create',
            'POST',
            array(
                'name' => 'LayoutName',
                'identifier' => 'Identifier',
                'delayoution' => 'Delayoution',
            )
        );
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/create');

        LayoutModel::fromIdentifier('Identifier')->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::editAction
     *
     * @return void
     */
    public function testEditActionWithInvalidId()
    {
        $this->dispatch('/admin/development/layout/edit/99999');
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/edit');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::editAction
     *
     * @return void
     */
    public function testEditAction()
    {
        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier'
            )
        );
        $layoutModel->save();

        $this->dispatch('/admin/development/layout/edit/' . $layoutModel->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/edit');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::editAction
     *
     * @return void
     */
    public function testEditActionWithInvalidPostData()
    {
        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier'
            )
        );
        $layoutModel->save();

        $this->dispatch(
            '/admin/development/layout/edit/' . $layoutModel->getId(),
            'POST',
            array(
            )
        );
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/edit');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::editAction
     *
     * @return void
     */
    public function testEditActionWithPostData()
    {
        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier'
            )
        );
        $layoutModel->save();

        $this->dispatch(
            '/admin/development/layout/edit/' . $layoutModel->getId(),
            'POST',
            array(
                'name' => 'LayoutName',
                'identifier' => 'Identifier',
                'delayoution' => 'Delayoution',
            )
        );
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/edit');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::deleteAction
     *
     * @return void
     */
    public function testDeleteAction()
    {
        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier'
            )
        );
        $layoutModel->save();

        $this->dispatch('/admin/development/layout/delete/' . $layoutModel->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/delete');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::deleteAction
     *
     * @return void
     */
    public function testDeleteActionWithInvalidId()
    {
        $this->dispatch('/admin/development/layout/delete/9999');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/delete');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::uploadAction
     *
     * @return void
     */
    public function testUploadAction()
    {
        $files = array(
            'upload' => array(
                'name' => __DIR__ . '/_files/upload.phtml',
                'type' => 'plain/text',
                'size' => 8,
                'tmp_name' => __DIR__ . '/_files/upload.phtml',
                'error' => 0,
            )
        );

        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier'
            )
        );
        $layoutModel->save();

        $this->dispatch('/admin/development/layout/upload/' . $layoutModel->getId());
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/upload');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::uploadAction
     *
     * @return void
     */
    public function testUploadActionWithoutId()
    {
        $files = array(
            'upload' => array(
                'name' => array(
                    'upload.phtml',
                    'test.phtml',
                    'test2.phtml',
                ),
                'type' => array(
                    'plain/text',
                    'plain/text',
                    'plain/text',
                ),
                'size' => array(
                    8,
                    8,
                    8,
                ),
                'tmp_name' => array(
                    __DIR__ . '/_files/upload.phtml',
                    __DIR__ . '/_files/test.phtml',
                    __DIR__ . '/_files/test.phtml',
                ),
                'error' => array(
                    UPLOAD_ERR_OK,
                    UPLOAD_ERR_OK,
                    UPLOAD_ERR_NO_FILE,
                ),
            ),
        );

        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'upload'
            )
        );
        $layoutModel->save();

        $this->dispatch('/admin/development/layout/upload');
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/upload');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::uploadAction
     *
     * @return void
     */
    public function testUploadActionWithEmptyFilesData()
    {
        $files = array('upload' => array());
        $this->dispatch('/admin/development/layout/upload');
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/upload');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::uploadAction
     *
     * @return void
     */
    public function testUploadActionWithInvalidId()
    {
        $this->dispatch('/admin/development/layout/upload/9999');
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/upload');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::downloadAction
     *
     * @return void
     */
    public function testDownloadAction()
    {
        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier',
                'content' => 'Test',
            )
        );
        $layoutModel->save();

        $this->dispatch('/admin/development/layout/download/' . $layoutModel->getId());
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/download');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::downloadAction
     *
     * @return void
     */
    public function testDownloadActionWithEmptyContent()
    {
        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier',
            )
        );
        $layoutModel->save();

        $this->dispatch('/admin/development/layout/download/' . $layoutModel->getId());
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/download');

        $layoutModel->delete();
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::downloadAction
     *
     * @return void
     */
    public function testDownloadActionWithInvalidId()
    {
        $this->dispatch('/admin/development/layout/download/9999');
        $this->assertResponseStatusCode(302);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/download');
    }

    /**
     * Test
     *
     * @covers Development\Controller\LayoutController::downloadAction
     *
     * @return void
     */
    public function testDownloadActionWithoutId()
    {
        $layoutModel = LayoutModel::fromArray(
            array(
                'name' => 'LayoutName',
                'identifier' => 'LayoutIdentifier',
                'content' => 'Content',
            )
        );
        $layoutModel->save();

        $this->dispatch('/admin/development/layout/download');
        $this->assertResponseStatusCode(200);

        $this->assertModuleName('Development');
        $this->assertControllerName('LayoutController');
        $this->assertControllerClass('LayoutController');
        $this->assertMatchedRouteName('development/layout/download');

        $layoutModel->delete();
    }
}
