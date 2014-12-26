<?php

namespace Lonja;

class Route {

  protected $destinations = array();


  /**
   * Adds a destination.
   *
   * @param $place
   * @param $distance
   */
  public function addDestination($place, $distance) {
    $this->destinations[$place]['distance'] = $distance;
  }

  /**
   * Add Product and price to a route.
   *
   * @param $place
   * @param $product
   * @param $price
   */
  public function addPricePerKgPerRoute($place, $product, $price) {
    $this->destinations[$place]['products'][$product] =  $price;
  }


  /**
   * Obtains destinations.
   *
   * @return array
   */
  public function getDestinations() {
    return $this->destinations;
  }

  /**
   * Calculate the best route detecting if the vehicle is loaded and prices are added to the route products.
   *
   * @param VehicleInterface $vehicle
   * @return mixed
   */
  public function calculateBestRoute(VehicleInterface $vehicle) {
    $this->checkEnoughInfo($vehicle);
    // Array to save amount total place.
    $routeTotalStorage = array();
    // take a look at the load.
    $load = $vehicle->getLoad();

    // Calculate the total per product per route.
    foreach($this->destinations as $destination => $destinationData) {
      $routeTotalStorage[$destination]['total'] = 0;
      foreach($destinationData['products'] as $product => $price) {
        $routeTotalStorage[$destination]['total'] += $price * $load[$product];
      }
      // Apply charges.
      $routeTotalStorage[$destination]['total'] = $vehicle->charges->applyCharges($routeTotalStorage[$destination]['total'], $destinationData['distance']);
    }
    // multiple routes with the same total???
    $bestRoute = array_keys($routeTotalStorage, max($routeTotalStorage));
    return $bestRoute[0];
  }

  /**
   * Verify at least one product and price were added per route.
   *
   * @return bool
   * @throws \Exception
   */
  public function hasProductPrices() {
    $missingDestinations = array();
    foreach ($this->destinations as $place => $info) {
      if (!isset($info['products'])) {
        $missingDestinations[] = $place;
      }
    }

    if (!empty($missingDestinations)) {
      throw new \Exception('Add at least one product and price for destinations:' . implode(',', $missingDestinations));
    }
    return true;
  }

  /**
   * Check that We have the Super Awesome Vehicle Loaded and at least one Charge applied.
   * This info is needed to calculate the best route.
   *
   * @throws \Exception
   */
  public function checkEnoughInfo($vehicle) {

    // Vehicle loaded.
    if ($vehicle->getCurrentWeight() == 0) {
      throw new \Exception('Please load the Van!');
    }

    // Check if each route has product and prices.
    $this->hasProductPrices();
  }

}
