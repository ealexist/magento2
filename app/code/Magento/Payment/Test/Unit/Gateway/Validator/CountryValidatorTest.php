<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Payment\Test\Unit\Gateway\Validator;

class CountryValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Payment\Gateway\Validator\CountryValidator */
    protected $model;

    /**
     * @var \Magento\Payment\Gateway\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Payment\Gateway\Validator\ResultInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    protected function setUp()
    {
        $this->configMock = $this->getMockBuilder('Magento\Payment\Gateway\ConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resultFactoryMock = $this->getMockBuilder('Magento\Payment\Gateway\Validator\ResultInterfaceFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = new \Magento\Payment\Gateway\Validator\CountryValidator(
            $this->configMock,
            $this->resultFactoryMock
        );
    }


    /**
     * @dataProvider validateAllowspecificTrueDataProvider
     */
    public function testValidateAllowspecificTrue($storeId, $country, $allowspecific, $specificcountry, $isValid)
    {
        $validationSubject = ['storeId' => $storeId, 'country' => $country];

        $this->configMock->expects($this->at(0))
            ->method('getValue')
            ->with('allowspecific', $storeId)
            ->willReturn($allowspecific);
        $this->configMock->expects($this->at(1))
            ->method('getValue')
            ->with('specificcountry', $storeId)
            ->willReturn($specificcountry);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnArgument(0));

        $this->assertEquals(['isValid' => $isValid], $this->model->validate($validationSubject));
    }

    public function validateAllowspecificTrueDataProvider()
    {
        return [
            [1, 'US', 1, 'US,UK,CA', true], //$storeId, $country, $allowspecific, $specificcountry, $isValid
            [1, 'BJ', 1, 'US,UK,CA', false]
        ];
    }

    /**
     * @dataProvider validateAllowspecificFalseDataProvider
     */
    public function testValidateAllowspecificFalse($storeId, $allowspecific, $isValid)
    {
        $validationSubject = ['storeId' => $storeId];

        $this->configMock->expects($this->at(0))
            ->method('getValue')
            ->with('allowspecific', $storeId)
            ->willReturn($allowspecific);

        $this->resultFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnArgument(0));

        $this->assertEquals(['isValid' => $isValid], $this->model->validate($validationSubject));
    }

    public function validateAllowspecificFalseDataProvider()
    {
        return [
            [1, 0, true] //$storeId, $allowspecific, $isValid
        ];
    }
}
