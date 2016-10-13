<?php

/**
 * Nextcloud - nextant
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Maxence Lange <maxence@pontapreta.net>
 * @copyright Maxence Lange 2016
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\Nextant\Service;

use OCA\Bookmarks\Controller\Lib\Bookmarks;

class IndexService
{

    private $bookmarkService;

    private $solrService;

    private $solrTools;

    private $miscService;

    private $output;

    public function __construct($bookmarkService, $solrService, $solrTools, $miscService)
    {
        $this->bookmarkService = $bookmarkService;
        
        $this->solrService = $solrService;
        $this->solrTools = $solrTools;
        $this->miscService = $miscService;
        $this->output = null;
    }

    public function setDebug($debug)
    {
        $this->miscService->setDebug($debug);
    }

    public function setOutput($output)
    {
        $this->output = $output;
    }

    private function message($line)
    {
        if ($this->output != null)
            $this->output->writeln($line);
    }

    public function extractBookmarks($userId)
    {
        if (! $this->bookmarkService->configured())
            return true;
        
        $this->solrService->setOwner($userId);
        
        $forceExtract = false;
        
        $db = \OC::$server->getDb();
        $bookmarks = Bookmarks::findBookmarks($userId, $db, 0, 'id', array(), false, - 1);
        
        foreach ($bookmarks as $bookmark) {
            
            if (! $forceExtract && $this->solrTools->isDocumentUpToDate('bookmarks', $bookmark['id'], $bookmark['lastmodified']))
                continue;
            
            if (! $this->solrService->extractFile($bookmark['url'], 'bookmarks', $bookmark['id'], $bookmark['url'], $bookmark['lastmodified'], $error))
                continue;
        }
        
        // public static function findUniqueBookmark($id, $userId, IDb $db) {
        
        return true;
    }
}




