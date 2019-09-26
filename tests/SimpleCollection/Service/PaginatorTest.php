<?php

namespace Tests\SimpleCollection\Service;

use PHPUnit\Framework\TestCase;
use SimpleCollection\AbstractCollection;
use SimpleCollection\ArrayCollection;
use SimpleCollection\AssocCollection;
use SimpleCollection\Service\Pagination\Paginator;

/**
 * Paginator test
 *
 * @copyright Felix Buchheim
 * @author    Felix Buchheim <hanibal4nothing@gmail.com>
 */
class PaginatorTest extends TestCase
{

    /**
     * Test paginator
     *
     * @param AbstractCollection $oCollection
     * @param int                $iPage
     * @param int                $iItemsPerPage
     * @param array              $expectedItemKeys
     *
     * @dataProvider collectionProvider
     */
    public function testPaginatorWithCollection(
        AbstractCollection $oCollection,
        $iPage,
        $iItemsPerPage,
        array $expectedItemKeys
    ) {
        $oPaginator = new Paginator($oCollection, $iPage, $iItemsPerPage);
        $oResult    = $oPaginator->getIterator();

        $this->assertInstanceOf(get_class($oCollection), $oResult);
        $this->assertEquals(count($expectedItemKeys), $oResult->count());
        foreach ($expectedItemKeys as $key => $value) {
            $this->assertTrue($oResult->offsetExists($key), 'Key ' . $key . ' does not exist');
            $this->assertEquals($oResult->get($key), $value);
        }
    }

    /**
     * Test exception if index is negative
     *
     * @param int $iPage
     * @param int $iItemsPerPage
     *
     * @dataProvider             negativePageProvider
     * @expectedException \OutOfBoundsException
     * @expectedExceptionMessage Start index cant no be lower then 1
     */
    public function testNegativePageException($iPage, $iItemsPerPage)
    {
        $oPaginator = new Paginator(new ArrayCollection(), $iPage, $iItemsPerPage);
        foreach ($oPaginator as $mItem) {
            echo 'does not happen';
        }
    }

    /**
     * Dataprovider for testNegativePageException
     *
     * @return array
     */
    public function negativePageProvider()
    {
        $aTestCases = array();

        $aTestCases['negativePage'] = array(
            'iPage'         => -2,
            'iItemsPerPage' => 2
        );

        $aTestCases['negativeItemsPerPage'] = array(
            'iPage'         => 2,
            'iItemsPerPage' => -2
        );

        $aTestCases['negativeItemsPerPage'] = array(
            'iPage'         => 0,
            'iItemsPerPage' => 2
        );

        return $aTestCases;
    }

    /**
     * Dataprovider for testPaginatorWithCollection
     *
     * @return array
     */
    public function collectionProvider()
    {
        $aTestCases = array();
        // ArrayCollection test
        $oCollection = new ArrayCollection(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));

        $aTestCases['pagePerItem'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 1,
            'iItemsPerPage'    => 1,
            'expectedItemKeys' => array(1)
        );

        $aTestCases['firstPageTwoItems'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 1,
            'iItemsPerPage'    => 2,
            'expectedItemKeys' => array(1, 2)
        );

        $aTestCases['3thPage3Items'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 3,
            'iItemsPerPage'    => 3,
            'expectedItemKeys' => array(7, 8, 9)
        );

        $aTestCases['1Page15Items'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 1,
            'iItemsPerPage'    => 15,
            'expectedItemKeys' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)
        );

        $aTestCases['2Page15Items'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 2,
            'iItemsPerPage'    => 15,
            'expectedItemKeys' => array()
        );

        $aTestCases['1Page0Items'] = array(
            'oCollection'      => new ArrayCollection(),
            'iPage'            => 1,
            'iItemsPerPage'    => 15,
            'expectedItemKeys' => array()
        );

        // Assoc Collection
        $oCollection = new AssocCollection(array('leet' => 1337, 'why' => 42, 4 => 'is random', 'PHP-v' => 7));

        $aTestCases['assocPagePerItem'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 1,
            'iItemsPerPage'    => 1,
            'expectedItemKeys' => array('leet' => 1337)
        );

        $aTestCases['assocFirstPageTwoItems'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 1,
            'iItemsPerPage'    => 2,
            'expectedItemKeys' => array('leet' => 1337, 'why' => 42)
        );

        $aTestCases['assoc2thPage2Items'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 2,
            'iItemsPerPage'    => 2,
            'expectedItemKeys' => array(4 => 'is random', 'PHP-v' => 7)
        );

        $aTestCases['assoc2thPage3Items'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 2,
            'iItemsPerPage'    => 3,
            'expectedItemKeys' => array('PHP-v' => 7)
        );

        $aTestCases['assoc1Page15Items'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 1,
            'iItemsPerPage'    => 15,
            'expectedItemKeys' => array('leet' => 1337, 'why' => 42, 4 => 'is random', 'PHP-v' => 7)
        );

        $aTestCases['assoc2Page15Items'] = array(
            'oCollection'      => $oCollection,
            'iPage'            => 2,
            'iItemsPerPage'    => 15,
            'expectedItemKeys' => array()
        );

        $aTestCases['assoc1Page0Items'] = array(
            'oCollection'      => new AssocCollection(),
            'iPage'            => 1,
            'iItemsPerPage'    => 15,
            'expectedItemKeys' => array()
        );

        return $aTestCases;
    }
}