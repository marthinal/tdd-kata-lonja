<?php

namespace Lonja\Tests;

use Lonja\Van;
use Lonja\Route;
use Lonja\VanCharges;


class VanTest extends \PHPUnit_Framework_TestCase
{

  protected $van;

  protected $route;

  protected $charges;


  protected function setUp() {

    $this->van = new Van(new VanCharges(5));

    $this->route = new Route();

  }


  /**
   * Tests that we can add load to the van.
   */
  public function testAddLoadToTheVan() {

    $expectedLoad = array('calamares' => 50, 'sardinas' => 100);

    // Add load.
    $this->van->addLoad('calamares', 50);
    $this->van->addLoad('sardinas', 100);

    // Asserts that the load was added as expected.
    $this->assertSame($expectedLoad, $this->van->getLoad());

    // Verify that the current weight is correct.
    $this->assertSame(150, $this->van->getCurrentWeight());

  }


  /**
   * Tests that we get the correct weight per product name.
   */
  public function testGetLoadPerProductName() {
    // Add load.
    $this->van->addLoad('calamares', 50);
    $this->van->addLoad('sardinas', 100);

    // Asserts that the load was added as expected.
    $this->assertSame(100, $this->van->getWeightByProductName('sardinas'));

    // Verify that the current weight is correct.
    $this->assertSame(50, $this->van->getWeightByProductName('calamares'));

  }


  /**
   * Tests cannot load more than max fixed by default.
   *
   * @expectedException        Exception
   * @expectedExceptionMessage MAX 200kg, YOUR CURRENT WEIGHT IS 150
   */
  public function testCanNotLoadMoreThanMaxInMyAwesomeVan() {
    // Add load. The max for a Van is 200 and we try for 250.
    $this->van->addLoad('calamares', 50);
    $this->van->addLoad('sardinas', 100);
    $this->van->addLoad('sardinas', 100);

  }

  /**
   * Tests that the routes, prices and distances are correctly added.
   */
  public function testAddRoute() {

    $expectedLoad = array('Madrid' => array('distance' => 100, 'products' => array('calamares' => 20)));

    // Adding a route.
    $this->route->addDestination('Madrid', 100);

    // Adding the price of a product per route.
    $this->route->addPricePerKgPerRoute('Madrid', 'calamares', 20);

    // Asserts that the Route with prices was added as expected.
    $this->assertSame($expectedLoad, $this->route->getDestinations());

  }

  /**
   * Tests Exception when vehicle but missing load.
   * @expectedException Exception
   * @expectedExceptionMessage Please load the Van!
   */
  public function testExceptionVehicleButNoLoad() {
    $this->route->calculateBestRoute($this->van);
  }

  /**
   * Tests Exception when we have routes and the vehicle is loaded but
   *  missing plroduct and prices per route.
   * @expectedException Exception
   * @expectedExceptionMessageRegExp "Add at least one product and price for destinations:Madrid,Valencia"
   */
  public function testExceptionMissingProductPricesPerRoute() {
    // Add load to the vehicle;
    $this->van->addLoad('calamares', 50);
    $this->van->addLoad('sardinas', 100);

    // Add Routes.
    $this->route->addDestination('Madrid', 100);
    $this->route->addDestination('Valencia', 200);

    $this->route->calculateBestRoute($this->van);
  }

  /**
   * Tests Exception when we have routes and the vehicle is loaded but
   *  missing plroduct and prices per route.
   * @expectedException Exception
   * @expectedExceptionMessageRegExp "Add at least one product and price for destinations:Valencia"
   */
  public function testExceptionMissingProductPricesOneRoute() {
    // Add load to the vehicle;
    $this->van->addLoad('calamares', 50);
    $this->van->addLoad('sardinas', 100);

    // Add Routes.
    $this->route->addDestination('Madrid', 100);
    $this->route->addDestination('Valencia', 200);

    // Adding the price of a product per route.
    $this->route->addPricePerKgPerRoute('Madrid', 'calamares', 20);

    $this->route->calculateBestRoute($this->van);
  }

  /**
   * Tests that the best Route was calculated as expected.
   */
  public function testCalculateBestRoute() {
    // Expected Best Route.
    $expectedBestRoute = 'Madrid';

    // Add load to the vehicle;
    $this->van->addLoad('calamares', 50);
    $this->van->addLoad('sardinas', 100);

    // Add Routes.
    $this->route->addDestination('Madrid', 100);
    $this->route->addDestination('Valencia', 100);
    $this->route->addDestination('Sevilla', 101);

    // Adding the price of a product per route.
    $this->route->addPricePerKgPerRoute('Madrid', 'calamares', 20);
    $this->route->addPricePerKgPerRoute('Valencia', 'calamares', 20);
    $this->route->addPricePerKgPerRoute('Madrid', 'sardinas', 101);
    $this->route->addPricePerKgPerRoute('Valencia', 'sardinas', 100);
    $this->route->addPricePerKgPerRoute('Sevilla', 'sardinas', 101);
    $this->route->addPricePerKgPerRoute('Sevilla', 'calamares', 20);

    // Apply Charges per 100km (€).
    $this->van->charges->setChargePerHundredKms('2');

    // The Best Route is Madrid.
    $this->assertSame($expectedBestRoute, $this->route->calculateBestRoute($this->van));

  }


}