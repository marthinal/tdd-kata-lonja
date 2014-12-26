<?php

namespace Lonja;

class Van implements VehicleInterface {

  const MAX_WEIGHT = 200;

  protected $currentWeight;

  protected $load = array();

  public $charges;


  public function __construct(VanCharges $charges) {

    $this->charges = $charges;

    $this->currentWeight = 0;
  }

  /**
   * Add load or charge the van.
   *
   * @param $name
   * @param $kg
   */
  public function addLoad($name, $kg) {
    if (($this->getCurrentWeight() + $kg) > self::MAX_WEIGHT) {
      throw new \Exception('MAX 200kg, YOUR CURRENT WEIGHT IS ' . $this->getCurrentWeight());
    }

    $this->load[$name] = $kg;
  }

  /**
   * Get the current load.
   *
   * @return array
   */
  public function getLoad() {

    return $this->load;
  }

  /**
   * Get the current weight by product name.
   *
   * @param $name
   * @return mixed
   */
  public function getWeightByProductName($name) {
    foreach($this->load as $k => $v) {
      if ($name == $k) {
        return $v;
      }
    }
    return null;
  }

  /**
   * Get the current Weight.
   *
   * @return int
   */
  public function getCurrentWeight() {

    $currentWeight = 0;

    foreach($this->load as $k => $v) {
      $currentWeight += $v;
    }
    return $currentWeight;

  }

}
