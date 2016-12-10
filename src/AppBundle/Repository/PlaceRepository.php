<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class PlaceRepository extends EntityRepository
{
    public function findAllPaginated($limit, $page, array $sorting = array())
    {
        $fields = array_keys($this->getClassMetadata()->fieldMappings);
        $queryBuilder = $this->createQueryBuilder('p');

        foreach ($fields as $field) {
            if (isset($sorting[$field])) {
                $direction = ($sorting[$field] === 'asc') ? 'asc' : 'desc';
                $queryBuilder->addOrderBy('p.'.$field, $direction);
            }
        }

        $pagerAdapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($pagerAdapter);
        $pager->setCurrentPage($page);
        $pager->setMaxPerPage($limit);

        return $pager;
    }
}