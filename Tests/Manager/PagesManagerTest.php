<?php
namespace Arnm\PagesBundle\Tests\Manager;

use Arnm\PagesBundle\Entity\PageRepository;

use Arnm\PagesBundle\Entity\Page;
use Symfony\Bundle\DoctrineBundle\Registry;
use Arnm\PagesBundle\Manager\PagesManager;

/**
 * PagesManager test case.
 */
class PagesManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests PagesManager->fetchFullHirarchy()
     *
     */
    public function testFetchFullHirarchy()
    {
        $doctrine = $this->getMock('Doctrine\Bundle\DoctrineBundle\Registry', array(
            'getManager'
        ), array(), '', false, true, true);
        $em = $this->getMock('Doctrine\ORM\EntityManager', array(
            'getRepository'
        ), array(), '', false, true, true);
        $pagesRepo = $this->getMock('Arnm\PagesBundle\Manager\PageRepository', array(
            'fetchFullHirarchy'
        ));

        $doctrine->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($em));

        $em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('ArnmPagesBundle:Page'))
            ->will($this->returnValue($pagesRepo));

        $pagesRepo->expects($this->once())
            ->method('fetchFullHirarchy')
            ->will($this->returnValue(array()));

        $pagesManager = new PagesManager($doctrine);
        //first of all there are no pages in DB
        $tree = $pagesManager->fetchPagesTree();
        $this->assertEquals(array(), $tree);
    }
}

