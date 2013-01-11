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
 * @category   Gc
 * @package    Library
 * @subpackage Core
 * @author     Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license    GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.html
 * @link       http://www.got-cms.com
 */

namespace Gc\Core\Updater\Adapter;

/**
 * Get and set config data
 *
 * @category   Gc
 * @package    Library
 * @subpackage Core
 */
class Git extends AbstractAdapter
{
    /**
     * Update
     *
     * @return string
     */
    public function update()
    {
        return shell_exec('cd ' . GC_APPLICATION_PATH . ' ; git fetch');
    }

    /**
     * Upgrade
     *
     * @return string
     */
    public function upgrade()
    {
        return shell_exec('cd ' . GC_APPLICATION_PATH . ' ; git checkout v' . $this->getLatest());
    }

    /**
     * Upgrade
     *
     * @param string $version Version to checkout
     * @return string
     */
    public function rollback($version)
    {
        return shell_exec('cd ' . GC_APPLICATION_PATH . ' ; git checkout v' . $version);
    }
}