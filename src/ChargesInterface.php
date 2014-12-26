<?php

namespace Lonja;

Interface ChargesInterface {

  public function setChargePerKm($charge, $kms);

  public function setChargeBasicPerLoad($charge);

  public function getChargeBasicPerLoad();

  public function applyCharges($total, $km);

}