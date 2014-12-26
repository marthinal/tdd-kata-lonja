<?php

namespace Lonja;


class VanCharges implements ChargesInterface {

  /**
   * Basic price for loading the Van.
   * @var
   */
  protected $loadCharge;

  /**
   * Discounts per Km.
   * @var
   */
  protected $chargePerKm;

  /**
   * Discount per 100Km.
   * @var
   */
  protected $chargePerHundredKm;

  protected $depreciation;


  // At least we need a basic charge.
  public function __construct($basicCharge) {
    if (NULL == is_int($basicCharge) && NULL == is_float($basicCharge)) {
      throw new \Exception('The basic charge per load must be an int or float');
    }
    $this->loadCharge = (int) $basicCharge;
  }


  public function setChargePerKm($currentTotal, $kms) {

  }

  public function setChargeBasicPerLoad($charge) {
    $this->loadCharge = $charge;
  }

  public function setChargePerHundredKms($charge) {
    $this->chargePerHundredKm = $charge;
  }

  public function getChargePerHundredKms() {
    return $this->chargePerHundredKm;
  }

  /**
   *
   * @return mixed
   */
  public function getChargeBasicPerLoad() {
    return $this->loadCharge;
  }


  public function applyCharges($total, $km) {
    // Basic Per Load.
    $total -= $this->getChargeBasicPerLoad();
    // Cherge Per 100km.
    $total -= $this->getChargePerHundredKms() * $km;
    // Depreciation per 100km.
    $total -= ($km / 100 * $total) / 100;

    return $total;
  }

}
