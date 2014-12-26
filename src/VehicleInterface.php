<?php

namespace Lonja;


Interface VehicleInterface {

  public function addLoad($name, $kg);

  public function getLoad();

  public function getCurrentWeight();

  public function getWeightByProductName($name);

}
