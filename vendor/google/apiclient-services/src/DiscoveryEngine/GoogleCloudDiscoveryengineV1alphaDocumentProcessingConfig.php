<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig extends \Google\Model
{
  protected $chunkingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig::class;
  protected $chunkingConfigDataType = '';
  protected $defaultParsingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig::class;
  protected $defaultParsingConfigDataType = '';
  /**
   * @var string
   */
  public $name;
  protected $parsingConfigOverridesType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig::class;
  protected $parsingConfigOverridesDataType = 'map';

  /**
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig
   */
  public function setChunkingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig $chunkingConfig)
  {
    $this->chunkingConfig = $chunkingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigChunkingConfig
   */
  public function getChunkingConfig()
  {
    return $this->chunkingConfig;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig
   */
  public function setDefaultParsingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig $defaultParsingConfig)
  {
    $this->defaultParsingConfig = $defaultParsingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig
   */
  public function getDefaultParsingConfig()
  {
    return $this->defaultParsingConfig;
  }
  /**
   * @param string
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig[]
   */
  public function setParsingConfigOverrides($parsingConfigOverrides)
  {
    $this->parsingConfigOverrides = $parsingConfigOverrides;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfigParsingConfig[]
   */
  public function getParsingConfigOverrides()
  {
    return $this->parsingConfigOverrides;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig');
