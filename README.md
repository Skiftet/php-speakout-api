### Installation

Run `composer require skiftet/speakout-api-client` in your project root to install the package.

### Usage

#### NOTE: The api currently only works with an in-development version of the Speakout api that isn't released yet

```php
use Skiftet\Speakout\Api\Client as Speakout;

$speakout = new Speakout([
    'endpoint' => '', // Set this to the url of a running Speakout deployment. E.g. 'http://localhost:3000'
    'user'     => '', // The username of a Speakout user
    'password' => '', // The correponding password
]);

/**
 * This will load request an array with all campaigns
 */
$campaigns = $speakout->campaigns()->all();

/**
 * This will request an array with all campaigns, sorted by action count
 */
$campaigns = $speakout->campaigns()->orderBy('actions')->get();

/**
 * You can also do nested queries for deeper filters. The example below will only
 * take actions since the 1st of March 2017 into account.
 */
$campaigns = $speakout->campaigns()->orderBy('actions')->has('actions', function (Query $query) {
    return $query->since('2017-03-01'); // this can also be an instance of \DateTime
})->get();
```
