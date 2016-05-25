<?php

/**
 * @file
 * Contains \DrupalCI\Tests\Plugin\BuildSteps\generic\CommandTest.
 */

namespace DrupalCI\Tests\Plugin\BuildSteps\generic;

use Docker\Container;
use DrupalCI\Plugin\BuildSteps\generic\ContainerCommand;
use DrupalCI\Tests\DrupalCITestCase;

/**
 * @coversDefaultClass DrupalCI\Plugin\BuildSteps\generic\ContainerCommand
 */
class ContainerCommandTest extends DrupalCITestCase {

  /**
   * @covers ::run
   */
  public function testRun() {
    $cmd = 'test_command test_argument';
    $instance = new Container([]);

    $body = $this->getMock('GuzzleHttp\Stream\StreamInterface');

    $response = $this->getMock('GuzzleHttp\Message\ResponseInterface');
    $response->expects($this->once())
      ->method('getBody')
      ->will($this->returnValue($body));

    $container_manager = $this->getMockBuilder('Docker\Manager\ContainerManager')
      ->disableOriginalConstructor()
      ->getMock();
    $container_manager->expects($this->once())
      ->method('find')
      ->will($this->returnValue($instance));
    $container_manager->expects($this->once())
      ->method('exec')
      ->with($instance, ['/bin/bash', '-c', $cmd], TRUE, TRUE, TRUE, TRUE)
      ->will($this->returnValue(1));
    $container_manager->expects($this->once())
      ->method('execstart')
      ->will($this->returnValue($response));
    $container_manager->expects($this->once())
      ->method('execinspect')
      ->will($this->returnValue((object) ['ExitCode' => 0]));

    $docker = $this->getMockBuilder('Docker\Docker')
      ->disableOriginalConstructor()
      ->setMethods(['getContainerManager'])
      ->getMock();
    $docker->expects($this->once())
      ->method('getContainerManager')
      ->will($this->returnValue($container_manager));

    $job = $this->getMockBuilder('DrupalCI\Plugin\JobTypes\JobInterface')
      ->getMockForAbstractClass();
    $job->expects($this->once())
      ->method('getDocker')
      ->will($this->returnValue($docker));
    $job->expects($this->once())
      ->method('getExecContainers')
      ->will($this->returnValue(['php' => [['id' => 'dockerci/php-5.4']]]));

    $command = new ContainerCommand();
    $command->run($job, $cmd);
  }

}
