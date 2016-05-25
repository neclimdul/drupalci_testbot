<?php

/**
 * @file
 * Contains \DrupalCI\Tests\Plugin\BuildSteps\setup\FetchTest.
 */

namespace DrupalCI\Tests\Plugin\BuildSteps\setup;

use DrupalCI\Plugin\BuildSteps\setup\Fetch;
use DrupalCI\Tests\DrupalCITestCase;
use GuzzleHttp\ClientInterface;

/**
 * @coversDefaultClass DrupalCI\Plugin\BuildSteps\setup\Fetch
 */
class FetchTest extends DrupalCITestCase {

  /**
   * @covers ::run
   */
  public function testRun() {
    $file = 'file.patch';
    $url = 'http://example.com/site/dir/' . $file;
    $dir = 'test/dir';

    $request = $this->getMock('GuzzleHttp\Message\RequestInterface');

    $http_client = $this->getMock('GuzzleHttp\ClientInterface');
    $http_client->expects($this->once())
      ->method('get')
      ->with($url, ['save_to' => "$dir/$file"])
      ->will($this->returnValue($request));

    $job_codebase = $this->getMock('DrupalCI\Job\CodeBase\JobCodebase');
    $job = $this->getMockBuilder('DrupalCI\Plugin\JobTypes\JobInterface')
      ->setMethods(['getJobCodebase'])
      ->getMockForAbstractClass();
    $job->expects($this->once())
      ->method('getJobCodebase')
      ->will($this->returnValue($job_codebase));

    $fetch = new TestFetch();
    $fetch->setValidate($dir);
    $fetch->setHttpClient($http_client);
    $fetch->run($job, [['url' => $url]]);
  }
}

class TestFetch extends Fetch {
  use TestSetupBaseTrait;

  function setHttpClient(ClientInterface $http_client) {
    $this->httpClient = $http_client;
  }
}
