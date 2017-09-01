<?php

class MarcelGeidel_CmsTree_Model_Tree extends Mage_Core_Model_Abstract
{
    protected $nodes   = array();
    protected $storeID = NULL;

    public function getMenuNodes($parentID = 0)
    {
        $menuNodes = array();

        foreach ($this->getNodes($parentID) as $node) {
            if ($node['include_in_menu']) {
                $node['children'] = $this->getMenuNodes($node['page_id']);

                $menuNodes[] = $node;
            }
        }

        return $menuNodes;
    }

    public function getNodes($parentID = -1)
    {
        $nodes = $this->nodes;

        if ($parentID >= 0) {
            foreach ($nodes as $id => $node) {
                if ($node['parent_id'] != $parentID) {
                    unset($nodes[$id]);
                }
            }
        }

        return $nodes;
    }

    public function load($storeID)
    {
        if (!$storeID) {
            Mage::throwException('CmsTree kann nicht geladen werden. StoreID fehlt.');
        }

        $this->nodes   = array();
        $this->storeID = $storeID;

        $query = 'SELECT * FROM marcelgeidel_cmstree WHERE store_id = :store_id ORDER BY position';
        $binds = array('store_id' => $this->storeID);
        $nodes = $this->getReader()->fetchAll($query, $binds);

        foreach ($nodes as $node) {
            $this->nodes[] = $node;
        }

        return $this;
    }

    protected function getReader()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_read');
    }

    public function update()
    {
        $oldNodes = $this->sortByID($this->nodes);

        $this->init($this->storeID);

        foreach ($this->nodes as $id => $node) {
            $pageID = $node['page_id'];

            if (isset($oldNodes[$pageID])) {
                $parentID = $oldNodes[$pageID]['parent_id'];

                if (!$this->checkCmsPage($parentID)) {
                    $parentID = 0;
                }

                $this->nodes[$id]['parent_id'] = $parentID;
                $this->nodes[$id]['position']  = $oldNodes[$pageID]['position'];
            }
        }

        return $this;
    }

    public function sortByID()
    {
        $reverse = array();

        foreach ($this->nodes as $node) {
            $reverse[$node['page_id']] = $node;
        }

        return $reverse;
    }

    public function init($storeID)
    {
        if (!$storeID) {
            Mage::throwException('CmsTree kann nicht initialisiert werden. StoreID fehlt.');
        }

        $this->nodes   = array();
        $this->storeID = $storeID;

        $cmsPages = Mage::getModel('cms/page')->getCollection()->addStoreFilter($storeID);

        $i = 0;

        foreach ($cmsPages as $cmsPage) {
            $this->nodes[] = array
            (
                'page_id'         => $cmsPage->getId(),
                'parent_id'       => 0,
                'store_id'        => $storeID,
                'title'           => $cmsPage->getTitle(),
                'position'        => $i++,
                'include_in_menu' => $cmsPage->getIncludeInMenu(),
                'css_class'       => $cmsPage->getCssClass(),
            );
        }

        return $this;
    }

    public function checkCmsPage($pageID)
    {
        $cmsPage = Mage::getModel('cms/page')->load($pageID);

        if ($cmsPage and $cmsPage->getId()) {
            $storeIDs = $cmsPage->getStoreId();

            if (in_array(0, $storeIDs) or in_array($this->storeID, $storeIDs)) {
                return true;
            }
        }

        return false;
    }

    public function save()
    {
        try {
            $this->getWriter()->beginTransaction();

            $this->clear();

            foreach ($this->nodes as $node) {
                $query = 'INSERT INTO marcelgeidel_cmstree (page_id, parent_id, store_id, title, position, include_in_menu, css_class) VALUES (:page_id, :parent_id, :store_id, :title, :position, :include_in_menu, :css_class)';

                $binds = array
                (
                    'page_id'         => $node['page_id'],
                    'parent_id'       => $node['parent_id'],
                    'store_id'        => $node['store_id'],
                    'title'           => $node['title'],
                    'position'        => $node['position'],
                    'include_in_menu' => $node['include_in_menu'],
                    'css_class'       => $node['css_class'],
                );

                $this->getWriter()->query($query, $binds);
            }

            $this->getWriter()->commit();
        } catch (Exception $e) {
            $this->getWriter()->rollback();

            return false;
        }

        return true;
    }

    protected function getWriter()
    {
        return Mage::getSingleton('core/resource')->getConnection('core_write');
    }

    public function clear()
    {
        $query = 'DELETE FROM marcelgeidel_cmstree WHERE store_id = :store_id';
        $binds = array('store_id' => $this->storeID);
        $this->getWriter()->query($query, $binds);

        return $this;
    }

    public function move($nodeID, $targetNodeID, $hitMode)
    {
        $nodePosition       = $this->getNodePosition($nodeID);
        $targetNodePosition = $this->getNodePosition($targetNodeID);

        // Erbschaft festlegen

        $parentID = $this->nodes[$targetNodePosition]['parent_id'];

        if ($hitMode == 'over') {
            $parentID = $targetNodeID;
        }

        $this->nodes[$nodePosition]['parent_id'] = $parentID;

        // Node von alter Position lösen

        $currentNode = array_slice($this->nodes, $nodePosition, 1, true);
        unset($this->nodes[$nodePosition]);

        // Node an neue Position setzen

        if ($hitMode == 'before') {
            $targetNodePosition = $this->getNodePosition($targetNodeID);
            array_splice($this->nodes, $targetNodePosition, 0, $currentNode);
        } elseif ($hitMode == 'after') {
            array_splice($this->nodes, $targetNodePosition, 0, $currentNode);
        } elseif ($hitMode == 'over') {
            array_splice($this->nodes, 99999, 0, $currentNode);
        }

        // Sortieren

        $this->sort();

        return $this;
    }

    public function getNodePosition($nodeID)
    {
        return array_search($nodeID, array_column($this->nodes, 'page_id'));
    }

    public function sort()
    {
        $nodes = $this->nodes;

        $i = 0;

        foreach ($nodes as $id => $node) {
            $nodes[$id]['position'] = $i++;
        }

        $this->nodes = $nodes;

        return $this;
    }
}
