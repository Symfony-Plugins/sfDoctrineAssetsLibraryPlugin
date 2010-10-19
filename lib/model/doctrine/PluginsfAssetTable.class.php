<?php

/**
 * PluginsfAssetTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginsfAssetTable extends Doctrine_Table
{
  
  /**
   * check if file exists in folder
   * @param  integer $folderId
   * @param  string  $filename
   * @return boolean
   */
  public function exists($folderId, $filename)
  {
    $query = $this->createQuery()
      ->where('folder_id = ?', $folderId)
      ->andWhere('filename = ?', $filename);

    return $query->count() > 0 ? true : false;
  }
  
  /**
   * Retrieves a sfAsset object from a relative URL like
   *    /medias/foo/bar.jpg
   * i.e. the kind of URL returned by $sf_asset->getUrl()
   */
  public  function retrieveFromUrl($url)
  {
    $url = sfAssetFolderTable::getInstance()->cleanPath($url, '/');
    list($relPath, $filename) = sfAssetsLibraryTools::splitPath($url, '/');
    $query = $this->createQuery('a')
      ->where('filename = ?', $filename)
      ->leftJoin('a.Folder f')
      ->andWhere('f.relative_path = ?', $relPath ?  $relPath : null)
      ;
    return $query->fetchOne();
  }
  
  /**
   * get pager for assets
   * @param  array   $params
   * @param  string  $sort
   * @param  integer $page
   * @param  integer $size
   * @return sfPager
   */
  public function getPager(array $params, $sort = 'name', $page = 1, $size = 20)
  {
    $query = $this->search($params, $sort);

    $pager = new sfDoctrinePager('sfAsset', $size);
    $pager->setQuery($query);
    $pager->setPage($page);
    $pager->init();

    return $pager;
  }
  
  /**
   * process search
   * @param  array    $params
   * @param  string   $sort
   * @return Doctrine_Query
   */
  protected function search(array $params, $sort = 'name')
  {
//    $c = new Criteria();
    $query = $this->createQuery('a');

    if (isset($params['folder_id']) && $params['folder_id'] !== '')
    {
      if (null!= $folder = sfAssetFolderTable::getInstance()->find($params['folder_id']))
      {
        if (false) $folder = new sfAssetFolder();
        $query->leftJoin('a.Folder f');
        $query->where('f.lft >= ?', $folder->getNode()->getLeftValue());
        $query->andWhere('f.rgt <= ?', $folder->getNode()->getRightValue());
      }
    }
//    if (isset($params['filename']['is_empty']))
//    {
//      $query->andWhere('filename = \'\' or filename is null', null);
////      $criterion = $c->getNewCriterion(self::FILENAME, '');
////      $criterion->addOr($c->getNewCriterion(self::FILENAME, null, Criteria::ISNULL));
////      $c->add($criterion);
//    }
//    else
    if (isset($params['filename']['text']) && strlen($params['filename']['text']))
    {
      $query->andWhere('filename like ?', '%' . trim($params['filename']['text'], '*%') . '%');
    }
    if (isset($params['author']['is_empty']))
    {
      $query->andWhere('author = \'\' or author is null');
    }
    elseif (isset($params['author']['text']) && strlen($params['author']['text']))
    {
      $query->andWhere('author like ?', '%' . trim($params['author']['text'], '*%') . '%');
    }
    if (isset($params['copyright']['is_empty']))
    {
      $query->andWhere('copyright = \'\' or copyright is null');
    }
    elseif (isset($params['copyright']['text']) && strlen($params['copyright']['text']))
    {
      $query->andWhere('copyright like ?', '%' . trim($params['copyright']['text'], '*%') . '%');
      $c->add(self::COPYRIGHT, '%' . trim($params['copyright']['text'], '*%') . '%', Criteria::LIKE);
    }
    if (isset($params['created_at']))
    {
      // TODO query
//      if (isset($params['created_at']['from']) && $params['created_at']['from'] !== array())  // TODO check this
//      {
//        $criterion = $c->getNewCriterion(self::CREATED_AT, $params['created_at']['from'], Criteria::GREATER_EQUAL);
//      }
//      if (isset($params['created_at']['to']) && $params['created_at']['to'] !== array())  // TODO check this
//      {
//        if (isset($criterion))
//        {
//          $criterion->addAnd($c->getNewCriterion(self::CREATED_AT, $params['created_at']['to'], Criteria::LESS_EQUAL));
//        }
//        else
//        {
//          $criterion = $c->getNewCriterion(self::CREATED_AT, $params['created_at']['to'], Criteria::LESS_EQUAL);
//        }
//      }
//      if (isset($criterion))
//      {
//        $c->add($criterion);
//      }
    }
    if (isset($params['description']['is_empty']))
    {
      $query->andWhere('description = \'\' or description is null');
    }
    else if (isset($params['description']) && $params['description'] !== '')
    {
      $query->andWhere('description like ?', '%' . trim($params['description']['text'], '*%') . '%');
    }

    switch ($sort)
    {
      case 'date':
        $query->orderBy('created_at DESC');
        break;
      default:
        $query->orderBy('filename ASC');
    }

    return $query;
  }
  
}