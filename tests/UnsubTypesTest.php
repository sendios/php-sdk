<?php

namespace Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sendios\Http\Request;
use Sendios\SendiosSdk;

class UnsubTypesTest extends TestCase
{
    /**
     * @var SendiosSdk
     */
    protected $service;

    /**
     * @var MockObject
     */
    protected $request;

    public function setUp(): void
    {
        $clientId = 123;
        $clientKey = 'a1s2d3f4g5h6j7k8l';
        $this->service = new SendiosSdk($clientId, $clientKey);
        $this->request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();
    }


    public function testGetList()
    {
        $this->request->expects($this->once())
            ->method('receive')
            ->with('unsubtypes/1')
            ->will($this->returnValue(['result' => true]));

        $this->service->request = $this->request;

        $result = $this->service->unsubTypes->getList(['id' => 1]);
        $this->assertEquals(['result' => true], $result);
    }

    public function testGetListWrongUserData()
    {
        $result = $this->service->unsubTypes->getList([]);
        $this->assertEquals(false, $result);
    }

    public function testSetDisabledTypes()
    {
        $this->request->expects($this->once())
            ->method('create')
            ->with('unsubtypes/1', ['type_ids' => [1, 2, 3]])
            ->will($this->returnValue(['result' => true]));

        $this->service->request = $this->request;

        $result = $this->service->unsubTypes->setDisabledTypes(['id' => 1], [1, 2, 3]);
        $this->assertEquals(['result' => true], $result);
    }

    public function testSetDisabledTypesWrongUserData()
    {
        $result = $this->service->unsubTypes->setDisabledTypes([], [1, 2, 3]);
        $this->assertEquals(false, $result);
    }

    public function testAddTypes()
    {
        $this->request->expects($this->once())
            ->method('create')
            ->with('unsubtypes/nodiff/' . 1, ['type_ids' => [1, 2, 3]])
            ->will($this->returnValue(['result' => true]));

        $this->service->request = $this->request;

        $result = $this->service->unsubTypes->addTypes(['id' => 1], [1, 2, 3]);
        $this->assertEquals(['result' => true], $result);
    }

    public function testAddTypesWrongUserData()
    {
        $result = $this->service->unsubTypes->addTypes([], [1, 2, 3]);
        $this->assertEquals(false, $result);
    }

    public function testRemoveTypes()
    {
        $this->request->expects($this->once())
            ->method('delete')
            ->with('unsubtypes/nodiff/' . 1, ['type_ids' => [1, 2, 3]])
            ->will($this->returnValue(['result' => true]));

        $this->service->request = $this->request;

        $result = $this->service->unsubTypes->removeTypes(['id' => 1], [1, 2, 3]);
        $this->assertEquals(['result' => true], $result);
    }

    public function testRemoveTypesWrongUserData()
    {
        $result = $this->service->unsubTypes->removeTypes([], [1, 2, 3]);
        $this->assertEquals(false, $result);
    }

    public function testRemoveAll()
    {
        $this->request->expects($this->once())
            ->method('delete')
            ->with('unsubtypes/all/' . 1)
            ->will($this->returnValue(['result' => true]));

        $this->service->request = $this->request;

        $result = $this->service->unsubTypes->removeAll(['id' => 1]);
        $this->assertEquals(['result' => true], $result);
    }

    public function testRemoveAllWrongUserData()
    {
        $result = $this->service->unsubTypes->removeAll([]);
        $this->assertEquals(false, $result);
    }
}
